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
            <div class="mt-8 border-t border-[#262626] pt-6">
                <h2 class="text-lg font-bold mb-4">Compartir este producto</h2>
                @php
                    $url = urlencode(url()->current());
                    $titulo = urlencode($producto->nombre);
                    $descripcion = urlencode('¡Mira este producto! Precio: Bs. ' . number_format($producto->precio, 2));
                    $imagen = $producto->imagenes->first() ? $producto->imagenes->first()->ruta : '';
                    $imagenUrl = $imagen ? urlencode(asset($imagen)) : '';
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="https://wa.me/?text={{ $titulo }}%20{{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-green-500/20 border border-green-500/40 hover:bg-green-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-blue-500/20 border border-blue-500/40 hover:bg-blue-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url={{ $url }}&media={{ $imagenUrl }}&description={{ $descripcion }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-red-500/20 border border-red-500/40 hover:bg-red-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-pinterest"></i> Pinterest
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-blue-800/20 border border-blue-800/40 hover:bg-blue-800/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $descripcion }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-sky-500/20 border border-sky-500/40 hover:bg-sky-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-twitter"></i> X (Twitter)
                    </a>
                    <a href="https://t.me/share/url?url={{ $url }}&text={{ $descripcion }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-cyan-500/20 border border-cyan-500/40 hover:bg-cyan-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-telegram-plane"></i> Telegram
                    </a>
                    <a href="mailto:?subject={{ $titulo }}&body={{ $descripcion }}%20{{ $url }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-yellow-500/20 border border-yellow-500/40 hover:bg-yellow-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                    <a href="https://www.reddit.com/submit?url={{ $url }}&title={{ $titulo }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-orange-500/20 border border-orange-500/40 hover:bg-orange-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-reddit-alien"></i> Reddit
                    </a>
                    <button onclick="copyLink('{{ url()->current() }}')"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm text-white
                   bg-gray-500/20 border border-gray-500/40 hover:bg-gray-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fas fa-link"></i> Copiar enlace
                    </button>
                </div>
            </div>
            <div id="toast"
                class="hidden fixed bottom-6 right-6 
           bg-emerald-900/70 text-emerald-50 
           px-5 py-3 rounded-2xl 
           shadow-xl border border-emerald-500/40 
           backdrop-blur-md
           text-sm font-medium 
           flex items-center gap-2
           animate-fade-in">
                ✅ Enlace copiado
            </div>
            <script>
                function copyLink(link) {
                    navigator.clipboard.writeText(link).then(() => {
                        let toast = document.getElementById("toast");
                        toast.classList.remove("hidden");
                        setTimeout(() => toast.classList.add("hidden"), 2000);
                    });
                }
            </script>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</div>
