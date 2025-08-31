<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;
use App\Models\Carrito;

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

        return view('payments.success', [
            'payment_intent' => $paymentIntentId,
            'payment_intent_client' => $paymentIntentClient,
        ]);
    }
}
