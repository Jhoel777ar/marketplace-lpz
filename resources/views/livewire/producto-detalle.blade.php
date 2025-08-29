<div>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-[#18181b] to-[#23272f] py-10">
        <div class="w-full max-w-3xl">
            <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 mb-6 text-sm bg-[#23272f] hover:bg-[#333] rounded-lg border border-[#333]">
                ← Volver
            </a>
            <div class="bg-[#171717] border border-[#262626] rounded-3xl p-8 shadow-2xl flex flex-col items-center">
            @if ($producto->imagenes->count())
                <div class="w-full flex justify-center mb-6">
                    <img src="{{ $producto->imagenes->first()->ruta }}" class="rounded-2xl object-cover w-72 h-72 shadow-lg border-4 border-[#23272f]" />
                </div>
            @endif
            <h1 class="text-3xl font-extrabold mb-2 text-center text-white tracking-tight">{{ $producto->nombre }}</h1>
            <p class="text-2xl font-bold text-emerald-400 mb-1 text-center">Bs. {{ number_format($producto->precio, 2) }}</p>
            <p class="text-base text-gray-400 mb-4 text-center">Stock: {{ $producto->stock }}</p>
            <div class="prose prose-invert max-w-none text-center mb-6">
                {!! $producto->descripcion !!}
            </div>
            <div class="mb-6 w-full">
                <h2 class="text-lg font-bold mb-2 text-white">Categorías</h2>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach ($producto->categorias as $cat)
                        <span class="px-3 py-1 bg-[#23272f] rounded-full text-sm text-white">{{ $cat->nombre }}</span>
                    @endforeach
                </div>
            </div>
            <div class="mb-6 w-full">
                <h2 class="text-lg font-bold mb-2 text-white">Reseñas</h2>
                <div class="flex flex-col items-center">
                @forelse($producto->resenas as $resena)
                    <div class="mt-3 p-4 border border-[#23272f] rounded-xl bg-[#23272f] w-full max-w-xl">
                        <p class="text-sm text-gray-300"><strong>{{ $resena->usuario->name }}</strong></p>
                        <p class="text-sm">⭐ {{ $resena->calificacion_producto }}/5</p>
                        <p class="text-gray-400">{{ $resena->reseña }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Este producto no tiene reseñas aún.</p>
                @endforelse
                </div>
            </div>
            <!-- Botones de redes sociales centrados -->
            <div class="mt-8 flex flex-col items-center">
                <h2 class="text-lg font-bold mb-2 text-white">¡Comparte este producto!</h2>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://wa.me/?text={{ urlencode('Mira este producto: ' . request()->fullUrl()) }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode('Mira este producto') }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-blue-400 hover:bg-blue-500 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-pink-500 hover:bg-pink-600 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode('Mira este producto') }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-blue-800 hover:bg-blue-900 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="inline-flex items-center justify-center w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-full shadow text-2xl">
                        <i class="fab fa-pinterest-p"></i>
                    </a>
                    <a href="mailto:?subject=Mira este producto&body={{ urlencode(request()->fullUrl()) }}" class="inline-flex items-center justify-center w-12 h-12 bg-gray-500 hover:bg-gray-600 text-white rounded-full shadow text-2xl">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</div>
