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
use App\Models\Envio;
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

    /**
     * Recibe la dirección de envío, crea venta y envio en estado pendiente,
     * crea un PaymentIntent asociado (metadata venta_id) y devuelve client_secret.
     */
    public function storeAddress(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        $carrito = Carrito::where('user_id', $user->id)->with('productos.producto')->first();
        if (!$carrito || $carrito->productos()->count() === 0) {
            return response()->json(['error' => 'Carrito vacío.'], 400);
        }

        $validated = $request->validate([
            'direccion' => 'required|string|max:1024',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:50',
        ]);

        // Crear venta y envio en transacción
        try {
            $venta = null;
            DB::transaction(function () use ($user, $carrito, $validated, &$venta) {
                $venta = Venta::create([
                    'user_id' => $user->id,
                    'total' => $carrito->total,
                    'estado' => 'pendiente',
                ]);

                foreach ($carrito->productos as $cp) {
                    VentaProducto::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $cp->producto_id,
                        'cantidad' => $cp->cantidad,
                        'subtotal' => $cp->subtotal,
                    ]);
                }

                Envio::create([
                    'venta_id' => $venta->id,
                    'direccion' => $validated['direccion'],
                    'ciudad' => $validated['ciudad'] ?? null,
                    'departamento' => $validated['departamento'] ?? null,
                    'pais' => $validated['pais'] ?? 'Bolivia',
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                    'estado' => 'pendiente',
                ]);
            });
            if (!$venta) {
                Log::error('Venta no creada en transacción.');
                return response()->json(['error' => 'Error al crear venta.'], 500);
            }
        } catch (\Throwable $e) {
            Log::error('Error creando venta/envio: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear venta/envio.'], 500);
        }

        // Crear PaymentIntent asociado a la venta
        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $currency = config('services.stripe.currency') ?? env('STRIPE_CURRENCY', 'usd');

        if (!$stripeSecret) {
            // En desarrollo sin Stripe retornamos empty string para clientSecret
            return response()->json(['clientSecret' => '', 'message' => 'Stripe no configurado', 'venta_id' => $venta->id]);
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $amountInCents = (int) round($carrito->total * 100);
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => [
                    'user_id' => $user->id,
                    'carrito_id' => $carrito->id,
                    'venta_id' => $venta->id,
                ],
            ]);

            return response()->json(['clientSecret' => $paymentIntent->client_secret ?? '', 'venta_id' => $venta->id]);
        } catch (\Throwable $e) {
            Log::error('Error creando PaymentIntent: ' . $e->getMessage());
            return response()->json(['error' => 'Error creando payment intent.'], 500);
        }
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

        // Intentar localizar la venta creada en storeAddress mediante metadata del PaymentIntent
        $ventaId = null;
        if (isset($pi) && isset($pi->metadata)) {
            $ventaId = $pi->metadata->venta_id ?? null;
        }

        try {
            DB::transaction(function () use ($user, $carrito, $paid, $ventaId) {
                // Si se encontró venta previa, actualizar su estado
                if ($ventaId) {
                    $venta = Venta::find($ventaId);
                    if ($venta) {
                        $venta->estado = $paid ? 'pagado' : $venta->estado ?? 'pendiente';
                        $venta->save();
                    }
                } else {
                    // Fallback: crear venta si no existía
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
                    }
                }

                // Descontar stock y emitir eventos (siempre que existan items en el carrito)
                foreach ($carrito->productos as $cp) {
                    $producto = Producto::where('id', $cp->producto_id)->lockForUpdate()->first();
                    if ($producto) {
                        $nuevoStock = max(0, $producto->stock - $cp->cantidad);
                        $producto->stock = $nuevoStock;
                        $producto->save();
                        event(new ProductChanged($producto, 'stock_updated'));
                    }
                }

                // Vaciar carrito
                $carrito->productos()->delete();
                $carrito->total = 0;
                $carrito->save();
            });
        } catch (\Throwable $e) {
            Log::error('Error al procesar venta existente: ' . $e->getMessage());
            return view('payments.success', [
                'payment_intent' => $paymentIntentId,
                'payment_intent_client' => $paymentIntentClient,
                'message' => 'Ocurrió un error al procesar la venta. Revisa los logs.',
            ]);
        }

        return view('payments.success', [
            'payment_intent' => $paymentIntentId,
            'payment_intent_client' => $paymentIntentClient,
            'message' => 'Venta registrada correctamente.',
        ]);
    }
}
