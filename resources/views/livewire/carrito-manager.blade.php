<div x-data="{
    abierto: false,
    mensajeVisible: true,
    mensajes: ['Compra ahora', 'Ahorra con cupones', 'Promoción limitada', 'Añade tus favoritos'],
    mensajeIndex: 0,
    cicloMensajes() {
        const interval = setInterval(() => {
            this.mensajeIndex = (this.mensajeIndex + 1) % this.mensajes.length
        }, 2500);

        setTimeout(() => {
            this.mensajeVisible = false;
            clearInterval(interval);
        }, 10000);
    }
}" x-init="cicloMensajes()">
    <div
        class="fixed top-5 left-1/2 -translate-x-1/2 sm:top-5 sm:right-5 sm:left-auto
           flex flex-row items-center space-x-3 pointer-events-none z-40">
        <template x-if="mensajeVisible">
            <div x-transition.opacity
                class="px-4 py-2 rounded-lg bg-black/50 backdrop-blur-md text-gray-100 font-semibold text-sm shadow-lg whitespace-nowrap pointer-events-auto">
                <span x-text="mensajes[mensajeIndex]"></span>
            </div>
        </template>
        <button @click="abierto = true"
            class="relative p-3 rounded-full shadow-xl transition-transform hover:scale-105
               bg-black/70 border border-gray-700/30 text-gray-100 pointer-events-auto">
            <flux:icon.shopping-cart class="w-6 h-6" />
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1 rounded-full">
                {{ count($items) }}
            </span>
        </button>
    </div>
    <div x-show="abierto" x-transition.opacity @click.outside="abierto = false"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-end z-50 pointer-events-auto">
        <div x-show="abierto" x-transition:enter="transition transform duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-11/12 sm:w-96 h-full bg-black/40 backdrop-blur-xl border-l border-gray-700/30 shadow-2xl p-6 flex flex-col pointer-events-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-100">Mi carrito</h3>
                <button @click="abierto = false" class="text-gray-200 hover:text-red-500 pointer-events-auto">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4">
                @forelse($items as $item)
                    <div class="flex justify-between items-center p-2 border-b border-gray-700/30">
                        <div>
                            <p class="text-sm font-medium text-gray-100">{{ $item->producto->nombre }}</p>
                            <p class="text-xs text-gray-400">Cant: {{ $item->cantidad }}</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-200">Bs. {{ number_format($item->subtotal, 2) }}</p>
                        <button wire:click="eliminarProducto({{ $item->id }})"
                            class="text-red-500 hover:text-red-700 text-xs font-bold ml-2 pointer-events-auto">X</button>
                    </div>
                @empty
                    <p class="text-gray-400 text-center">Tu carrito está vacío.</p>
                @endforelse
            </div>

            <!-- Total -->
            <div class="mt-4 font-bold text-right text-gray-100">
                Total: Bs. {{ number_format($total, 2) }}
            </div>

            <a href="{{ route('carrito') }}"
                class="mt-3 block bg-black/70 hover:bg-black/80 text-gray-100 text-sm py-2 rounded-lg text-center shadow-lg transition pointer-events-auto">
                Ver carrito
            </a>
        </div>
    </div>
</div>
