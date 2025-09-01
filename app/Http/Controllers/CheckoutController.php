<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use App\Models\Carrito;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Events\ProductChanged;
use App\Models\Producto;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        $carrito = Carrito::where('user_id', $user->id)->with('productos.producto.imagenes')->first();

        if (!$carrito || $carrito->productos()->count() === 0) {
            return redirect()->route('carrito.ver')->with('error', 'Tu carrito está vacío.');
        }

        $total = (float) $carrito->total;
        $amountInCents = (int) round($total * 100);

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $stripeKey = config('services.stripe.key') ?? env('STRIPE_KEY');
        $currency = config('services.stripe.currency') ?? env('STRIPE_CURRENCY', 'usd');

        if (!$stripeSecret) {
            return view('checkout', [
                'clientSecret' => null,
                'total' => number_format($total, 2),
                'user' => $user,
                'items' => $carrito->productos->map(function ($cp) {
                    return [
                        'id' => $cp->producto->id ?? null,
                        'nombre' => $cp->producto->nombre ?? 'Producto eliminado',
                        'precio' => $cp->producto->precio ?? 0,
                        'cantidad' => $cp->cantidad,
                        'subtotal' => $cp->subtotal,
                        'imagen' => optional($cp->producto->imagenes->first())->ruta ?? null,
                    ];
                })->toArray(),
                'stripeKey' => $stripeKey,
            ]);
        }

        $stripe = new StripeClient($stripeSecret);

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amountInCents,
            'currency' => $currency,
            'metadata' => [
                'user_id' => $user->id,
                'carrito_id' => $carrito->id,
            ],
        ]);

        return view('checkout', [
            'clientSecret' => $paymentIntent->client_secret,
            'total' => number_format($total, 2),
            'user' => $user,
            'items' => $carrito->productos->map(function ($cp) {
                return [
                    'id' => $cp->producto->id ?? null,
                    'nombre' => $cp->producto->nombre ?? 'Producto eliminado',
                    'precio' => $cp->producto->precio ?? 0,
                    'cantidad' => $cp->cantidad,
                    'subtotal' => $cp->subtotal,
                    'imagen' => optional($cp->producto->imagenes->first())->ruta ?? null,
                ];
            })->toArray(),
            'stripeKey' => $stripeKey,
        ]);
    }

    public function success(Request $request)
    {
   
        $paymentIntentId = $request->query('payment_intent');
        $paymentIntentClient = $request->query('payment_intent_client_secret');

        $user = Auth::user();
        if (!$user) {
            return view('payments.success', [
                'payment_intent' => $paymentIntentId,
                'payment_intent_client' => $paymentIntentClient,
                'message' => 'Usuario no autenticado, no se pudo crear la venta.',
            ]);
        }

        $carrito = Carrito::where('user_id', $user->id)->with('productos.producto')->first();
        if (!$carrito || $carrito->productos()->count() === 0) {
            return view('payments.success', [
                'payment_intent' => $paymentIntentId,
                'payment_intent_client' => $paymentIntentClient,
                'message' => 'No hay productos en el carrito.',
            ]);
        }

        // Determinar si el pago fue completado revisando el PaymentIntent en Stripe cuando sea posible
        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $paid = false;

        if ($stripeSecret && $paymentIntentId) {
            try {
                $stripe = new StripeClient($stripeSecret);
                $pi = $stripe->paymentIntents->retrieve($paymentIntentId);
                $paid = isset($pi->status) && $pi->status === 'succeeded';
            } catch (\Throwable $e) {
                Log::warning('No se pudo recuperar PaymentIntent: ' . $e->getMessage());
                // en caso de error asumimos pendiente para evitar marcar como pagado sin confirmación
                $paid = false;
            }
        } else {
            // Si no hay Stripe configurado (modo local), asumimos que fue pagado para permitir flujo de pruebas
            $paid = true;
        }

        // Crear la venta y los items dentro de una transacción
        try {
            DB::transaction(function () use ($user, $carrito, $paid) {
                $venta = Venta::create([
                    'user_id' => $user->id,
                    'total' => $carrito->total,
                    'estado' => $paid ? 'pagado' : 'pendiente',
                ]);

                foreach ($carrito->productos as $cp) {
                    VentaProducto::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $cp->producto_id,
                        'cantidad' => $cp->cantidad,
                        'subtotal' => $cp->subtotal,
                    ]);

                    // Descontar stock de forma segura (lock for update para concurrencia)
                    $producto = Producto::where('id', $cp->producto_id)->lockForUpdate()->first();
                    if ($producto) {
                        $nuevoStock = max(0, $producto->stock - $cp->cantidad);
                        $producto->stock = $nuevoStock;
                        $producto->save();

                        // Emitir evento para notificar cambio de producto (solo el stock)
                        event(new ProductChanged($producto, 'stock_updated'));
                    }
                }

                // Vaciar carrito
                $carrito->productos()->delete();
                $carrito->total = 0;
                $carrito->save();
            });
        } catch (\Throwable $e) {
            Log::error('Error al crear venta: ' . $e->getMessage());
            return view('payments.success', [
                'payment_intent' => $paymentIntentId,
                'payment_intent_client' => $paymentIntentClient,
                'message' => 'Ocurrió un error al registrar la venta. Revisa los logs.',
            ]);
        }

        return view('payments.success', [
            'payment_intent' => $paymentIntentId,
            'payment_intent_client' => $paymentIntentClient,
            'message' => 'Venta registrada correctamente.',
        ]);
    }
}
