<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 p-4">

    <div class="col-span-full">
        <input type="text" wire:model.live="search" placeholder="🔍 Buscar producto..."
            class="w-full p-3 rounded-2xl border border-white/20 bg-black/20 backdrop-blur-md text-white placeholder-gray-400
                   focus:outline-none focus:ring-2 focus:ring-green-400 shadow-md transition">
    </div>
    @forelse ($productos as $producto)
        <div
            class="relative flex flex-col items-center rounded-2xl p-5
                   bg-black/20 backdrop-blur-xl
                   border border-white/20 shadow-xl
                   transition-transform hover:scale-[1.03] duration-300 group">

            @if ($producto->destacado)
                <span
                    class="absolute top-3 left-3 bg-gradient-to-r from-green-400 to-green-600 
                           text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                    🌟 Destacado
                </span>
            @endif

            <div class="w-full h-48 rounded-xl overflow-hidden mb-4 shadow-md">
                <img src="{{ $producto->imagenes->first()?->ruta ?? 'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png' }}"
                    alt="{{ $producto->nombre }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            </div>

            <h3 class="text-lg font-bold text-white text-center mb-1 line-clamp-2">
                {{ $producto->nombre }}
            </h3>

            <p
                class="font-extrabold text-transparent text-xl bg-clip-text bg-gradient-to-r from-green-400 to-blue-400 mb-3">
                Bs.{{ number_format($producto->precio, 2) }}
            </p>

            @php
                $calificacion = $producto->resenas()->aprobadas()->avg('calificacion_producto');
                $calificacion = $calificacion ? round($calificacion) : 0;
            @endphp
            @if ($calificacion > 0)
                <div class="flex mb-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 @if ($i <= $calificacion) text-yellow-400 @else text-gray-500/50 @endif"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.963a1 1 0 0 0 .95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 0 0-.364 1.118l1.287 3.963c.3.921-.755 1.688-1.54 1.118l-3.37-2.447a1 1 0 0 0-1.176 0l-3.37 2.447c-.784.57-1.838-.197-1.539-1.118l1.286-3.963a1 1 0 0 0-.364-1.118L2.064 9.39c-.783-.57-.38-1.81.588-1.81h4.165a1 1 0 0 0 .95-.69l1.286-3.963z" />
                        </svg>
                    @endfor
                </div>
            @endif

            <button wire:click="verProducto({{ $producto->id }})" wire:loading.attr="disabled"
                wire:target="verProducto({{ $producto->id }})"
                class="relative px-6 py-2 rounded-xl bg-gradient-to-r from-green-400 to-green-600 
                       text-white font-semibold shadow-md
                       hover:from-green-500 hover:to-emerald-500
                       transition-all duration-300 flex items-center justify-center">

                <span wire:loading.remove wire:target="verProducto({{ $producto->id }})">
                    Ver producto
                </span>

                <span wire:loading wire:target="verProducto({{ $producto->id }})" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                        </path>
                    </svg>
                    Redirigiendo...
                </span>
            </button>
        </div>
    @empty
        <p class="col-span-full text-center text-gray-400 text-lg">😔 No se encontraron productos.</p>
    @endforelse

    <div class="mt-8 col-span-full flex justify-center">
        {{ $productos->links() }}
    </div>
</div>
