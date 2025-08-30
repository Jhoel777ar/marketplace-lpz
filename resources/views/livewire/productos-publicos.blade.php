<div wire:key="productos-publicos-component" class="flex flex-col gap-8 p-4 md:p-6 max-w-7xl mx-auto">
    @livewire('carrito-manager')
    <div
        class="backdrop-blur-lg bg-white/80 dark:bg-[#171717] border border-gray-200 dark:border-[#262626] rounded-2xl p-5 shadow-xl shadow-black/10 dark:shadow-black/20 transition-all duration-300">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="flex flex-wrap gap-3 items-center">
                    <div class="relative flex-1 min-w-48">
                        <flux:input type="text" placeholder="Buscar productos..." wire:model.live="search"
                            icon="magnifying-glass"
                            class="backdrop-blur-sm bg-gray-100/70 dark:bg-[#262626]/70 border border-gray-200 dark:border-[#262626] 
                       text-gray-800 dark:text-white placeholder:text-gray-400 focus:ring-1 focus:ring-primary/30" />
                    </div>
                    <flux:select wire:model.live="orderBy" icon="bars-arrow-up"
                        class="backdrop-blur-sm bg-gray-100/70 dark:bg-[#262626]/70 border border-gray-200 dark:border-[#262626] 
                   text-gray-800 dark:text-white min-w-48">
                        <option value="recientes">Más recientes</option>
                        <option value="precio_asc">Precio: menor a mayor</option>
                        <option value="precio_desc">Precio: mayor a menor</option>
                        <option value="popularidad">Más populares</option>
                    </flux:select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                <span>Precio:</span>
                <flux:input type="number" placeholder="Mín" wire:model.live.debounce.300ms="priceMin" step="0.01"
                    min="0" size="sm"
                    class="w-28 backdrop-blur-sm bg-gray-100/70 dark:bg-[#262626]/70 border border-gray-200 dark:border-[#262626] 
                           text-gray-800 dark:text-white" />
                <span class="text-gray-400 dark:text-gray-500">—</span>
                <flux:input type="number" placeholder="Máx" wire:model.live.debounce.300ms="priceMax" step="0.01"
                    min="0" size="sm"
                    class="w-28 backdrop-blur-sm bg-gray-100/70 dark:bg-[#262626]/70 border border-gray-200 dark:border-[#262626] 
                           text-gray-800 dark:text-white" />
            </div>
        </div>
    </div>

    <div wire:loading wire:target="search,priceMin,priceMax,orderBy" class="flex justify-center py-6">
        <flux:icon.loading class="size-6 text-gray-400 animate-spin" />
        <span class="ml-2 text-gray-500 dark:text-gray-400 font-medium">Buscando productos...</span>
    </div>

    <div class="grid md:grid-cols-3 gap-6" wire:loading.remove>
        @forelse($productos as $producto)
            <div wire:key="producto-{{ $producto->id }}"
                class="group relative flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-[#171717] 
                       border border-gray-200 dark:border-[#262626] shadow-lg hover:shadow-xl 
                       hover:shadow-gray-200/50 dark:hover:shadow-[#262626]/30 transition-all duration-300 backdrop-blur-sm">
                <div class="h-48 w-full overflow-hidden relative">
                    @php
                        $imagen =
                            $producto->imagenes->first()?->ruta ??
                            'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png';
                    @endphp
                    <img src="{{ $imagen }}" alt="{{ $producto->nombre }}"
                        class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                </div>
                <div class="p-5 flex flex-col flex-1 text-gray-800 dark:text-gray-100">
                    <div class="flex gap-1 mb-3">
                        @if ($producto->destacado)
                            <div class="text-amber-500 text-sm font-bold flex items-center gap-1">
                                <flux:icon.star variant="solid" class="size-4" />
                                <span>Destacado</span>
                            </div>
                        @endif
                        @if ($producto->created_at->diffInDays(now()) <= 7)
                            <div
                                class="bg-emerald-100 dark:bg-emerald-900/70 text-emerald-700 dark:text-emerald-200 
                                       text-xs px-2 py-0.5 rounded-full flex items-center border border-emerald-200 dark:border-emerald-800/50">
                                <flux:icon.clock variant="micro" class="mr-1" /> Nuevo
                            </div>
                        @endif
                    </div>
                    <h2
                        class="font-bold text-lg text-gray-900 dark:text-gray-50 group-hover:text-primary-600 dark:group-hover:text-gray-200 transition-colors">
                        {{ $producto->nombre }}
                    </h2>
                    <p class="font-bold text-lg text-gray-700 dark:text-gray-200 mt-1">Bs.
                        {{ number_format($producto->precio, 2) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Stock: {{ $producto->stock }}</p>
                    @if ($producto->emprendedor)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Vendedor:
                            {{ $producto->emprendedor->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ubicación:
                            {{ $producto->emprendedor->ubicacion }}</p>
                    @endif
                    @php
                        $promedio = $producto->resenas->avg('calificacion_producto');
                    @endphp
                    <div class="flex items-center gap-1 mt-3 text-sm">
                        @if ($promedio)
                            <flux:icon.star variant="solid" class="size-4 text-amber-400" />
                            <span class="text-amber-500">{{ round($promedio, 1) }}/5</span>
                        @else
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Sin reseñas</span>
                        @endif
                    </div>
                    <div class="mt-auto pt-5 flex gap-2">
                        <flux:button wire:click="$dispatch('agregarAlCarrito', { id: {{ $producto->id }} })"
                            color="slate" size="sm"
                            class="flex-1 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-[#262626] dark:hover:bg-[#333] border border-gray-300 dark:border-[#333]"
                            icon="shopping-cart" x-on:click="$dispatch('agregando')">
                            Agregar
                        </flux:button>
                        <flux:button as="a" href="{{ route('productos.detalle', $producto->id) }}"
                            variant="outline" color="neutral" size="sm"
                            class="flex-1 text-sm border-gray-300 dark:border-[#262626] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#262626]/50">
                            Ver
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-10">
                <flux:icon.inbox variant="solid"
                    class="size-12 text-gray-400 dark:text-gray-600 mx-auto mb-3 opacity-60" />
                <p class="text-gray-500 dark:text-gray-400 text-lg">No se encontraron productos.</p>
            </div>
        @endforelse
        <div x-data="{ show: false }" x-show="show" x-transition.opacity.duration.300ms x-cloak
            x-on:agregando.window="show = true; setTimeout(() => show = false, 1500);"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md">
            <div
                class="flex flex-col items-center justify-center px-8 py-6 rounded-2xl shadow-2xl 
               bg-gradient-to-r from-gray-900/80 to-black/80 dark:from-black/80 dark:to-black/80 
               text-white border border-white/20 backdrop-blur-xl animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white animate-spin mb-3" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                        stroke-dasharray="60" stroke-linecap="round" />
                </svg>
                <span class="text-xl font-bold tracking-wide text-center">
                    Agregando al carrito...
                </span>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-center" wire:loading.remove>
        <div
            class="inline-flex items-center space-x-0 rounded-xl bg-white dark:bg-[#171717] border border-gray-200 dark:border-[#262626] p-1 backdrop-blur-sm shadow-lg">
            @foreach ($productos->links()->elements[0] as $page => $url)
                @if ($page == $productos->currentPage())
                    <span
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-[#262626] rounded-lg transition">
                        {{ $page }}
                    </span>
                @else
                    <button wire:click="gotoPage({{ $page }}, '{{ $productos->getPageName() }}')"
                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#262626]/70 rounded-lg transition duration-200">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
            @if ($productos->previousPageUrl())
                <button wire:click="previousPage('{{ $productos->getPageName() }}')"
                    class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-[#262626]/70 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif
            @if ($productos->nextPageUrl())
                <button wire:click="nextPage('{{ $productos->getPageName() }}')"
                    class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-[#262626]/70 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
