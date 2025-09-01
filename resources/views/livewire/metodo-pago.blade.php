<div >
    <div
        class="bg-white/85 dark:bg-[rgb(23,23,23)] backdrop-blur-xl border border-gray-200 dark:border-gray-700/50 rounded-3xl shadow-2xl overflow-hidden transition-all duration-300">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Método de Pago</h2>
        </div>
        <div class="flex flex-col lg:flex-row gap-6 px-6 py-6">
            <div class="flex-1">
                <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Productos en el carrito</h3>
                <div class="max-h-96 overflow-y-auto space-y-3">
                    @foreach ($items as $item)
                        <div
                            class="flex justify-between items-center p-3 bg-white/70 dark:bg-[rgb(38,38,38)] rounded-2xl backdrop-blur-sm border border-gray-100 dark:border-gray-700/50 shadow-sm hover:bg-white/90 dark:hover:bg-[rgb(38,38,38)]/80 transition-all">
                            <span class="text-gray-800 dark:text-gray-200 font-medium">
                                {{ $item->producto->nombre }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">(x{{ $item->cantidad }})</span>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Bs.
                                {{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-right space-y-1">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Subtotal: Bs. {{ number_format($subtotal, 2) }}
                    </p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Total: Bs. {{ number_format($total, 2) }}
                    </p>
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-6" id="cupon-direccion-section">
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" wire:model="codigoCupon" placeholder="Código de cupón"
                        class="flex-1 rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)] dark:text-gray-100 p-3 shadow-inner focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-500/40 focus:border-gray-400 dark:focus:border-gray-500 outline-none transition-all backdrop-blur-sm">
                    <button wire:click="aplicarCupon"
                        class="bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-ticket-alt text-sm opacity-80"></i>
                        Aplicar Cupón
                    </button>
                </div>
                @if (!empty($cuponesAplicados))
                    <ul class="text-sm text-emerald-700 dark:text-emerald-400 space-y-1">
                        @foreach ($cuponesAplicados as $c)
                            <li
                                class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-xl border border-emerald-100 dark:border-emerald-800/40">
                                <span class="text-emerald-600 dark:text-emerald-300 text-lg">✓</span>
                                <span>{{ $c->codigo }} aplicado ({{ $c->descuento }}% descuento)</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Dirección de Envío</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block mb-1 text-gray-700 dark:text-gray-300 font-medium">País</label>
                            <select wire:model.defer="pais"
                                class="w-full rounded-2xl border p-3 dark:bg-[rgb(38,38,38)] dark:text-gray-100">
                                <option value="">Seleccione un país</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Chile">Chile</option>
                                <option value="Perú">Perú</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Brasil">Brasil</option>
                                <option value="Venezuela">Venezuela</option>
                            </select>
                            @error('pais')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <input wire:model.defer="direccion" type="text" placeholder="Dirección completa"
                            class="rounded-2xl border p-3 dark:bg-[rgb(38,38,38)] dark:text-gray-100">
                        @error('direccion')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <input wire:model.defer="ciudad" type="text" placeholder="Ciudad"
                            class="rounded-2xl border p-3 dark:bg-[rgb(38,38,38)] dark:text-gray-100">
                        @error('ciudad')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <input wire:model.defer="departamento" type="text" placeholder="Departamento"
                            class="rounded-2xl border p-3 dark:bg-[rgb(38,38,38)] dark:text-gray-100">
                        @error('departamento')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <input wire:model.defer="codigo_postal" type="text" placeholder="Código postal"
                            class="rounded-2xl border p-3 dark:bg-[rgb(38,38,38)] dark:text-gray-100">
                        @error('codigo_postal')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 flex flex-col sm:flex-row justify-between gap-3 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/40 dark:bg-[rgb(23,23,23)] backdrop-blur-sm"
            id="botones-section">
            <a href="{{ route('carrito') }}"
                class="w-full sm:w-auto text-center bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i> Volver al carrito
            </a>
            <button id="pagarAhora"
                class="w-full sm:w-auto text-center bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-lock text-sm"></i> Pagar ahora
            </button>
        </div>
        <div id="stripe-section"
            class="mt-6 px-6 py-6 bg-gray-100 dark:bg-[rgb(38,38,38)] rounded-2xl shadow-inner hidden">
            <button id="volver" class="mb-4 text-gray-800 dark:text-gray-100 hover:underline">← Volver</button>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pago con Tarjeta</h3>

            <div id="card-element"
                class="rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)]/70 p-4 shadow-inner">
            </div>
            <div id="card-errors" role="alert" class="text-red-500 mt-2"></div>

            <div class="mt-4 text-right font-bold text-gray-900 dark:text-white">
                Total a pagar: Bs. {{ number_format($total, 2) }}
            </div>

            <button id="confirmarPago" wire:click="pagarConStripe(tokenId)" wire:loading.attr="disabled"
                class="mt-4 w-full rounded-2xl px-6 py-3 font-semibold
       bg-green-600/30 dark:bg-green-700/30
       backdrop-blur-md
       text-white
       border border-green-500/50
       hover:bg-green-600/50 dark:hover:bg-green-700/50
       shadow-lg hover:shadow-2xl
       transition-all duration-300
       hover:scale-105 active:scale-95
       focus:outline-none focus:ring-4 focus:ring-green-400/60">
                <span wire:loading.remove>Finalizar Compra</span>
                <span wire:loading>Procesando...</span>
            </button>

        </div>

        @if ($paso >= 0)
            <div class="w-full max-w-3xl mx-auto mt-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center sm:justify-start justify-center">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full 
                        {{ $paso >= 0 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                0
                            </div>
                            <div class="ml-2 font-medium">Inicio</div>
                        </div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full 
                        {{ $paso >= 1 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                1
                            </div>
                            <div class="ml-2 font-medium">Verificando stock</div>
                        </div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full 
                        {{ $paso >= 2 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                2
                            </div>
                            <div class="ml-2 font-medium">Procesando pago</div>
                        </div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full 
                        {{ $paso >= 3 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                3
                            </div>
                            <div class="ml-2 font-medium">Confirmando</div>
                        </div>
                    </div>
                    <div class="flex-1 sm:text-right text-center">
                        <div class="flex items-center justify-center sm:justify-end">
                            <div
                                class="w-10 h-10 flex items-center justify-center rounded-full 
                        {{ $paso >= 4 ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                                4
                            </div>
                            <div class="ml-2 font-medium">Éxito</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                        style="width: {{ (($paso + 1) / 5) * 100 }}%"></div>
                </div>
            </div>
        @endif

    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pagarBtn = document.getElementById('pagarAhora');
        const stripeSection = document.getElementById('stripe-section');
        const cuponDireccionSection = document.getElementById('cupon-direccion-section');
        const botonesSection = document.getElementById('botones-section');
        const volverBtn = document.getElementById('volver');
        const confirmarPagoBtn = document.getElementById('confirmarPago');

        let stripe;
        let card;

        function inicializarStripe() {
            const cardElementContainer = document.getElementById('card-element');
            cardElementContainer.innerHTML = '';

            stripe = Stripe("{{ env('STRIPE_KEY') }}");
            const elements = stripe.elements();
            const style = {
                base: {
                    color: "#fff",
                    fontSize: "16px",
                    "::placeholder": {
                        color: "#888"
                    }
                },
                invalid: {
                    color: "#f44336"
                }
            };
            card = elements.create("card", {
                style
            });
            card.mount("#card-element");
        }
        pagarBtn.addEventListener('click', () => {
            stripeSection.classList.remove('hidden');
            cuponDireccionSection.classList.add('hidden');
            botonesSection.classList.add('hidden');
            inicializarStripe();
        });
        volverBtn.addEventListener('click', () => {
            stripeSection.classList.add('hidden');
            cuponDireccionSection.classList.remove('hidden');
            botonesSection.classList.remove('hidden');
        });
        confirmarPagoBtn.addEventListener('click', async () => {
            const {
                token,
                error
            } = await stripe.createToken(card);
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
            } else {
                @this.pagarConStripe(token.id);
            }
        });
    });
</script>
