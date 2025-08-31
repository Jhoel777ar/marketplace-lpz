<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Checkout</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-50">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Pago</h1>

        <!-- Datos del comprador -->
        <div class="mb-4">
            <h2 class="font-semibold">Quien compra</h2>
            @if(isset($user))
                <p>{{ $user->name }} — {{ $user->email }}</p>
            @else
                <p>Invitado</p>
            @endif
        </div>

        <!-- Detalle de productos -->
        <div class="mb-4">
            <h2 class="font-semibold">Detalles</h2>
            @if(!empty($items) && count($items) > 0)
                <ul class="space-y-3">
                    @foreach($items as $it)
                        <li class="flex items-center space-x-3">
                            <div class="w-14 h-14 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                                @if($it['imagen'])
                                    <img src="{{ $it['imagen'] }}" alt="{{ $it['nombre'] }}" class="w-full h-full object-contain">
                                @else
                                    <span class="text-sm text-gray-500">No imagen</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">{{ $it['nombre'] }}</div>
                                <div class="text-sm text-gray-600">{{ $it['cantidad'] }} x {{ number_format($it['precio'],2) }} Bs</div>
                            </div>
                            <div class="font-semibold">{{ number_format($it['subtotal'],2) }} Bs</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No hay productos en el carrito.</p>
            @endif
        </div>

        <p class="mb-4">Total a pagar: <strong>{{ $total }} Bs</strong></p>

        @if($clientSecret)
        <div id="payment-element" data-client-secret="{{ $clientSecret }}" data-stripe-key="{{ $stripeKey ?? '' }}"></div>
        <button id="submit" class="w-full bg-black text-white py-3 rounded mt-6">Pagar</button>

        <div id="error-message" class="text-red-600 mt-3"></div>

        <script>
            const mountData = document.getElementById('payment-element').dataset;
            const stripeKey = mountData.stripeKey;
            const clientSecret = mountData.clientSecret;

            if (!stripeKey) {
                document.getElementById('error-message').textContent = 'Stripe public key not configured.';
                document.getElementById('submit').disabled = true;
            }

            const stripe = Stripe(stripeKey);

            (async () => {
                const elements = stripe.elements({clientSecret});
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');

                const submit = document.getElementById('submit');
                submit.addEventListener('click', async () => {
                    submit.disabled = true;
                    const {error} = await stripe.confirmPayment({
                        elements,
                        confirmParams: {return_url: window.location.origin + '/payments/success'},
                    });
                    if (error) {
                        document.getElementById('error-message').textContent = error.message;
                        submit.disabled = false;
                    }
                });
            })();
        </script>

        @else
        <p class="text-yellow-600">Stripe no está configurado. Contacta al administrador.</p>
        @endif
    </div>
</body>
</html>
