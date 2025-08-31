<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($productos as $producto)
        <div
            class="flex flex-col items-center rounded-2xl p-4 
                    bg-black/20 dark:bg-black/30 backdrop-blur-md
                    border border-white/20 dark:border-gray-700
                    shadow-lg transition-transform hover:scale-105 duration-300">

            <div class="w-full h-48 rounded-xl overflow-hidden mb-4">
                <img src="{{ $producto->imagenes->first()?->ruta ?? 'https://via.placeholder.com/300x200' }}"
                    alt="{{ $producto->nombre }}"
                    class="w-full h-full object-cover transition-transform hover:scale-110 duration-500">
            </div>

            <h3 class="text-lg font-semibold text-gray-100 text-center mb-1">
                {{ $producto->nombre }}
            </h3>

            <p class="font-bold text-indigo-400 text-xl mb-3">
                Bs.{{ number_format($producto->precio, 2) }}
            </p>

            <button wire:click="verProducto({{ $producto->id }})"
                class="px-6 py-2 rounded-xl bg-indigo-600 text-white font-semibold
                           hover:bg-indigo-500 shadow-md hover:shadow-lg transition-all duration-300">
                Ver
            </button>
        </div>
    @endforeach
    <div class="mt-6">
        {{ $productos->links() }}
    </div>

</div>
