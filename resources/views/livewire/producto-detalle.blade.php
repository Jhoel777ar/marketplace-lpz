<div>
    <div class="max-w-5xl mx-auto p-6 text-gray-100">
        <a href="javascript:history.back()"
            class="inline-flex items-center px-4 py-2 mb-4 text-sm bg-[#262626] hover:bg-[#333] rounded-lg border border-[#333]">
            ← Volver
        </a>
        <div class="bg-[#171717] border border-[#262626] rounded-2xl p-6 shadow-lg">
            <h1 class="text-2xl font-bold mb-3">{{ $producto->nombre }}</h1>
            <p class="text-lg font-semibold text-emerald-400">Bs. {{ number_format($producto->precio, 2) }}</p>
            <p class="text-sm text-gray-400 mb-4">Stock: {{ $producto->stock }}</p>
            @if ($producto->imagenes->count())
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                    @foreach ($producto->imagenes as $img)
                        <img src="{{ $img->ruta }}" class="rounded-lg object-cover w-full h-48" />
                    @endforeach
                </div>
            @endif
            <div class="prose prose-invert max-w-none">
                {!! $producto->descripcion !!}
            </div>
            <div class="mt-6">
                <h2 class="text-lg font-bold">Categorías</h2>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach ($producto->categorias as $cat)
                        <span class="px-3 py-1 bg-[#262626] rounded-full text-sm">{{ $cat->nombre }}</span>
                    @endforeach
                </div>
            </div>
            <div class="mt-6">
                <h2 class="text-lg font-bold">Reseñas</h2>
                @forelse($producto->resenas as $resena)
                    <div class="mt-3 p-3 border border-[#262626] rounded-lg">
                        <p class="text-sm text-gray-300"><strong>{{ $resena->usuario->name }}</strong></p>
                        <p class="text-sm">⭐ {{ $resena->calificacion_producto }}/5</p>
                        <p class="text-gray-400">{{ $resena->reseña }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Este producto no tiene reseñas aún.</p>
                @endforelse
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</div>
