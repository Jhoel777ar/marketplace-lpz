<div class="w-full max-w-4xl mx-auto p-4 md:p-6 lg:p-8">
    <div
        class="bg-white/85 dark:bg-[rgb(23,23,23)] backdrop-blur-xl border border-gray-200 dark:border-gray-700/50 rounded-3xl shadow-2xl overflow-hidden transition-all duration-300">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Método de Pago</h2>
        </div>
        <div class="max-h-60 overflow-y-auto px-6 py-4 space-y-2">
            @foreach ($items as $item)
                <div
                    class="flex justify-between items-center p-3 bg-white/70 dark:bg-[rgb(38,38,38)] rounded-2xl backdrop-blur-sm border border-gray-100 dark:border-gray-700/50 shadow-sm hover:bg-white/90 dark:hover:bg-[rgb(38,38,38)]/80 transition-all">
                    <span class="text-gray-800 dark:text-gray-200 font-medium">
                        {{ $item->producto->nombre }} <span
                            class="text-sm text-gray-500 dark:text-gray-400">(x{{ $item->cantidad }})</span>
                    </span>
                    <span class="font-bold text-gray-900 dark:text-white">Bs.
                        {{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/50">
            <div class="text-right space-y-1">
                <p class="text-gray-600 dark:text-gray-400 text-sm">Subtotal: Bs. {{ number_format($subtotal, 2) }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">Total: Bs. {{ number_format($total, 2) }}</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row gap-3">
            <input type="text" wire:model="codigoCupon" placeholder="Código de cupón"
                class="flex-1 rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)] dark:text-gray-100 p-3 shadow-inner focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-500/40 focus:border-gray-400 dark:focus:border-gray-500 outline-none transition-all backdrop-blur-sm">
            <button wire:click="aplicarCupon"
                class="bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-ticket-alt text-sm opacity-80"></i>
                Aplicar Cupón
            </button>
        </div>
        @if (!empty($cuponesAplicados))
            <div class="px-6 pb-4">
                <ul class="text-sm text-emerald-700 dark:text-emerald-400 space-y-1">
                    @foreach ($cuponesAplicados as $c)
                        <li
                            class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-xl border border-emerald-100 dark:border-emerald-800/40">
                            <span class="text-emerald-600 dark:text-emerald-300 text-lg">✓</span>
                            <span>{{ $c->codigo }} aplicado ({{ $c->descuento }}% descuento)</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="px-6 py-6 border-t border-gray-100 dark:border-gray-700/50">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pago con Tarjeta</h3>
            <div
                class="relative rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)]/70 p-4 shadow-inner flex items-center mb-4 backdrop-blur-sm">
                <input type="text" inputmode="numeric" name="number" placeholder="1234 1234 1234 1234"
                    autocomplete="cc-number" maxlength="19" pattern="[0-9 ]*"
                    class="w-full bg-transparent text-gray-900 dark:text-white text-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\d{4})/g, '$1 ').trim().slice(0, 19)">
                <div class="flex space-x-2 ml-3">
                    <svg class="w-8 h-5 opacity-70" viewBox="0 0 24 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="16" rx="2" fill="#FFF" stroke="#DDD" />
                        <path
                            d="M2.788 5.914A7.201 7.201 0 0 0 1 5.237l.028-.125h2.737c.371.013.672.125.77.519l.595 2.836.182.854 1.666-4.21h1.799l-2.674 6.167H4.304L2.788 5.914Z"
                            fill="#1434CB" />
                    </svg>
                    <svg class="w-8 h-5 opacity-70" viewBox="0 0 24 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="16" rx="2" fill="#252525" />
                        <circle cx="9" cy="8" r="5" fill="#eb001b" />
                        <circle cx="15" cy="8" r="5" fill="#f79e1b" />
                        <path d="M12 4c1.214.912 2 2.364 2 4s-.786 3.088-2 4c-1.214-.912-2-2.364-2-4s.786-3.088 2-4z"
                            fill="#ff5f00" />
                    </svg>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" placeholder="MM/YY" autocomplete="cc-exp" maxlength="5"
                    pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                    class="rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)] dark:text-gray-100 p-3 shadow-inner focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-500/40 outline-none transition-all text-lg backdrop-blur-sm"
                    oninput="formatExpiry(this)">
                <input type="text" placeholder="CVC" autocomplete="cc-csc" inputmode="numeric" maxlength="4"
                    pattern="[0-9]{3,4}"
                    class="rounded-2xl border border-gray-300 dark:border-gray-600 dark:bg-[rgb(38,38,38)] dark:text-gray-100 p-3 shadow-inner focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-500/40 outline-none transition-all text-lg backdrop-blur-sm"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)">
            </div>
        </div>
        <div
            class="px-6 py-6 flex flex-col sm:flex-row justify-between gap-3 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/40 dark:bg-[rgb(23,23,23)] backdrop-blur-sm">
            <a href="{{ route('carrito') }}"
                class="w-full sm:w-auto text-center bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left text-sm"></i>
                Volver al carrito
            </a>
            <button
                class="w-full sm:w-auto text-center bg-transparent hover:bg-white/20 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100 font-semibold rounded-2xl px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-md hover:shadow-lg backdrop-blur-md transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-lock text-sm"></i>
                Pagar ahora
            </button>
        </div>
    </div>
</div>
