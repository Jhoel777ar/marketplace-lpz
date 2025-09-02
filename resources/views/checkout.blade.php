<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="stripe-key" content="{{ $stripeKey ?? '' }}">
    <meta name="checkout-store" content="{{ route('checkout.store') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        <div id="shipping-form" class="mb-4">
            <h2 class="font-semibold">Dirección de envío</h2>
            <form id="addressForm" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium">Dirección</label>
                    <input name="direccion" required class="w-full border rounded p-2" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Departamento</label>
                        <select name="departamento" id="departamentoSelect" required class="w-full border rounded p-2"></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Ciudad</label>
                        <select name="ciudad" id="ciudadSelect" required class="w-full border rounded p-2"></select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input name="pais" placeholder="País" value="Bolivia" readonly class="border rounded p-2" />
                    <input name="codigo_postal" placeholder="Código postal" class="border rounded p-2" />
                </div>
                <div>
                    <button id="preparePayment" type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Continuar para pagar</button>
                </div>
            </form>
        </div>

        <div id="payment-area" style="display:none">
            <div id="payment-element" data-stripe-key="{{ $stripeKey ?? '' }}"></div>
            <button id="submit" class="w-full bg-black text-white py-3 rounded mt-6">Pagar</button>
            <div id="error-message" class="text-red-600 mt-3"></div>
        </div>

        <script>
            const stripePublicKey = document.querySelector('meta[name="stripe-key"]').content || '';
            const addressForm = document.getElementById('addressForm');
            const paymentArea = document.getElementById('payment-area');
            let stripe = null;
            let elements = null;

            // MAPA de departamentos/ciudades de Bolivia (para poblar selects)
            const boliviaMap = {
                "La Paz": ["La Paz","El Alto","Viacha","Achacachi","Copacabana"],
                "Cochabamba": ["Cochabamba","Quillacollo","Sacaba","Tiquipaya","Colcapirhua"],
                "Santa Cruz": ["Santa Cruz de la Sierra","Montero","La Guardia","Warnes","Cotoca"],
                "Beni": ["Trinidad","Rurrenabaque","Guayaramerín","Reyes"],
                "Pando": ["Cobija","Riberalta","Bolpebra"],
                "Tarija": ["Tarija","Yacuiba","Villamontes"],
                "Chuquisaca": ["Sucre","Padilla","Zudáñez"],
                "Oruro": ["Oruro","Huanuni","Caracollo"],
                "Potosí": ["Potosí","Uyuni","Villazón"]
            };

            const departamentoSelect = document.getElementById('departamentoSelect');
            const ciudadSelect = document.getElementById('ciudadSelect');

            function populateDepartamentos() {
                departamentoSelect.innerHTML = '';
                Object.keys(boliviaMap).forEach((dep) => {
                    const opt = document.createElement('option');
                    opt.value = dep;
                    opt.textContent = dep;
                    departamentoSelect.appendChild(opt);
                });
            }

            function populateCiudades(dep) {
                ciudadSelect.innerHTML = '';
                const ciudades = boliviaMap[dep] || [];
                ciudades.forEach((c) => {
                    const opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    ciudadSelect.appendChild(opt);
                });
            }

            // Inicializar selects ahora
            populateDepartamentos();
            const defaultDep = departamentoSelect.options[0]?.value;
            populateCiudades(defaultDep);
            departamentoSelect.addEventListener('change', (ev) => {
                populateCiudades(ev.target.value);
            });

            addressForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(addressForm);
                const payload = {};
                formData.forEach((v,k) => payload[k]=v);

                document.getElementById('preparePayment').disabled = true;

                try {
            const res = await fetch(document.querySelector('meta[name="checkout-store"]').content, {
                        method: 'POST',
                        headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Error al preparar pago');

                    const clientSecret = data.clientSecret;
                    const ventaId = data.venta_id;

                    // Si Stripe no está configurado, mostrar mensaje y no intentar montar elementos
                    if (!stripePublicKey) {
                        document.getElementById('error-message').textContent = 'Stripe public key not configured.';
                        return;
                    }

                    stripe = Stripe(stripePublicKey);

                    paymentArea.style.display = '';
                    document.getElementById('shipping-form').style.display = 'none';

                    elements = stripe.elements({clientSecret});
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

                } catch (err) {
                    document.getElementById('error-message').textContent = err.message || 'Error procesando dirección';
                    document.getElementById('preparePayment').disabled = false;
                }
            });
        </script>

    </div>
</body>
</html>
