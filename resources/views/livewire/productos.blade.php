<div class="">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($productos as $producto)
        <a href="{{ route('login') }}" 
           class="block bg-gray-100 rounded-xl shadow p-2 relative flex flex-col hover:shadow-lg transition-shadow duration-300">
            <div class="flex flex-col flex-1 dark:text-gray-50">
                <div class="h-48 w-full overflow-hidden relative mx-auto">
                    @php
                        $imagen = $producto->imagenes->first()?->ruta ??
                        'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png';
                    @endphp
                    <img src="{{ $imagen }}" alt="{{ $producto->nombre }}"
                        class="rounded-xl w-full h-full transition-transform duration-500 group-hover:scale-105" />
                </div>        
            </div>
            <h2 class="font-bold text-lg text-black-50 group-hover:text-gray-700 transition-colors mt-2">
                {{ $producto->nombre }}
            </h2>
            <p class="font-bold text-lg text-black-200 mt-1">Bs. {{ number_format($producto->precio, 2) }}</p>
            <p class="text-sm text-black-400 mt-1">Stock: {{ $producto->stock }}</p>
            @if ($producto->emprendedor)
                <p class="text-sm text-black-300 mt-2">{{ $producto->emprendedor->name }}</p>
            @endif
        </a>
        @endforeach
    </div>
</div>
