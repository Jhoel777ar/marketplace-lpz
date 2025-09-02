<div>
    <h1 class="text-3xl font-extrabold mb-6 text-gray-900 dark:text-gray-100">
        Mi carrito
    </h1>
    <div
        class="bg-white/40 dark:bg-[rgb(23,23,23)]/90 backdrop-blur-xl border border-gray-200 dark:border-[rgb(38,38,38)] rounded-3xl shadow-2xl p-6 space-y-6 transition-colors duration-500">

        @forelse($items as $item)
            <div
                class="flex flex-col md:flex-row justify-between items-center border-b border-gray-300 dark:border-[rgb(38,38,38)] pb-4 last:border-b-0 gap-4
                    hover:shadow-2xl hover:scale-[1.01] transition-transform duration-300 ease-in-out">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <img src="{{ $item->imagenPrincipal }}" alt="Imagen del producto"
                        class="w-24 h-24 object-cover rounded-xl shadow-lg transition-transform hover:scale-110 hover:shadow-2xl">
                    <div class="flex flex-col gap-1">
                        <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $item->producto->nombre }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Agregado:
                            {{ $item->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Precio: Bs. {{ number_format($item->producto->precio, 2) }} |
                            Stock: {{ $item->producto->stock }}
                        </p>
                        @if ($item->promedioResena)
                            <p class="text-yellow-400 font-semibold">⭐ {{ number_format($item->promedioResena, 1) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4 md:mt-0">
                    <input type="number" min="1" max="{{ $item->producto->stock }}"
                        wire:change="actualizarCantidad({{ $item->id }}, $event.target.value)"
                        value="{{ $item->cantidad }}"
                        class="w-16 border border-gray-300 dark:border-[rgb(38,38,38)] rounded-2xl p-2 text-center text-gray-900 dark:text-gray-100 
              bg-white/50 dark:bg-[rgb(23,23,23)]/70 backdrop-blur-sm shadow-inner 
              focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-300" />
                    <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">Bs.
                        {{ number_format($item->subtotal, 2) }}</p>
                    <button wire:click="eliminar({{ $item->id }})" wire:loading.attr="disabled"
                        wire:target="eliminar({{ $item->id }})"
                        class="text-red-500 hover:text-red-700 bg-red-500/10 dark:bg-red-600/20 px-3 py-1 rounded-2xl border border-red-200 dark:border-red-700 
           backdrop-blur-sm shadow-md hover:scale-110 transition-all duration-300">
                        <span wire:loading.remove wire:target="eliminar({{ $item->id }})">X</span>
                        <span wire:loading wire:target="eliminar({{ $item->id }})">Eliminando...</span>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-6">No tienes productos en tu carrito.</p>
        @endforelse
    </div>
    <div
        class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 
            bg-white/30 dark:bg-[rgb(23,23,23)]/90 backdrop-blur-xl border border-gray-200 dark:border-[rgb(38,38,38)] rounded-2xl shadow-2xl p-4 transition-colors duration-500">

        <div class="font-bold text-2xl text-gray-900 dark:text-gray-100">
            Total: Bs. {{ number_format($total, 2) }}
        </div>

        @if ($items->count() > 0)
            <button x-data="{ verificando: false }"
                @click="verificando = true; $wire.comprarAhora(); setTimeout(() => verificando = false, 7000)"
                class="flex items-center gap-2 px-6 py-3 rounded-2xl text-white
           bg-gray-900/60 dark:bg-gray-500/20 backdrop-blur-lg
           border border-gray-300 dark:border-gray-600 shadow-xl
           hover:scale-105 transition-transform duration-300">
                <template x-if="!verificando">
                    <span><i class="fas fa-shopping-cart"></i> Comprar ahora</span>
                </template>
                <template x-if="verificando">
                    <span>Verificando...</span>
                </template>
            </button>
        @endif
    </div>
</div>
