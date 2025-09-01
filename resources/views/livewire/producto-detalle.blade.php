<div>
    <div class="max-w-6xl mx-auto p-6 text-gray-900 dark:text-gray-100">
        <a href="#"
            onclick="if(document.referrer){history.back();} else {window.location.href='{{ route('dashboard') }}';} return false;"
            class="inline-flex items-center px-4 py-2 mb-4 text-sm rounded-xl 
                   bg-gray-200 dark:bg-[#262626] 
                   hover:bg-gray-300 dark:hover:bg-[#333] 
                   border border-gray-300 dark:border-[#333] 
                   transition-all duration-300">
            ← Volver
        </a>
        <div
            class="bg-white dark:bg-[#171717] border border-gray-200 dark:border-[#262626] 
                    rounded-2xl p-6 shadow-lg transition-all duration-300">
            <h1 class="text-2xl md:text-3xl font-bold mb-3">{{ $producto->nombre }}</h1>
            <p class="text-xl md:text-2xl font-semibold text-emerald-600 dark:text-emerald-400">
                Bs. {{ number_format($producto->precio, 2) }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Stock: {{ $producto->stock }}</p>
            @php
                $imagenes = $producto->imagenes->take(5);
                $imagenDefault =
                    'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png';
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                @if ($imagenes->count())
                    @foreach ($imagenes as $img)
                        <div class="relative group overflow-hidden rounded-xl bg-gray-100 dark:bg-[#262626]">
                            <img src="{{ $img->ruta ?? $imagenDefault }}"
                                class="w-full h-40 md:h-48 object-cover transform group-hover:scale-105 transition-transform duration-500 cursor-zoom-in"
                                onclick="openImageModal('{{ $img->ruta ?? $imagenDefault }}')" />
                        </div>
                    @endforeach
                @else
                    <div class="relative group overflow-hidden rounded-xl bg-gray-100 dark:bg-[#262626]">
                        <img src="{{ $imagenDefault }}"
                            class="w-full h-40 md:h-48 object-cover transition-transform duration-500" />
                    </div>
                @endif
            </div>
            <div class="mb-4 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                <span class="font-semibold">Vendido por:</span>
                @if ($producto->emprendedor)
                    <span>{{ $producto->emprendedor->name }}</span>
                    @if ($producto->emprendedor->ubicacion)
                        <span class="text-gray-500 dark:text-gray-400">({{ $producto->emprendedor->ubicacion }})</span>
                    @endif
                @else
                    <span class="text-gray-500 dark:text-gray-400">Desconocido</span>
                @endif
            </div>
            <div class="prose dark:prose-invert max-w-none">
                {!! $producto->descripcion !!}
            </div>
            <div class="mt-6">
                <h2 class="text-lg font-bold">Categorías</h2>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach ($producto->categorias as $cat)
                        <span
                            class="px-3 py-1 rounded-full text-sm 
                                     bg-gray-200 dark:bg-[#262626] 
                                     text-gray-700 dark:text-gray-200">
                            {{ $cat->nombre }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-3">
                <button wire:click="agregarAlCarrito"
                    class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl text-sm
               font-semibold text-white
               bg-green-500/20 dark:bg-green-500/30
               border border-green-500/30 dark:border-green-500/50
               backdrop-blur-md shadow-md
               hover:bg-green-500/30 dark:hover:bg-green-500/50
               transition-all duration-300
               w-full md:w-auto">
                    <i class="fas fa-cart-plus"></i>
                    Agregar al carrito
                </button>
                <a href="{{ route('carrito') }}"
                    class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl text-sm
              font-semibold text-gray-800 dark:text-white
              bg-gray-200/30 dark:bg-gray-700/30
              border border-gray-300 dark:border-gray-600
              backdrop-blur-md shadow-md
              hover:bg-gray-300/40 dark:hover:bg-gray-600/50
              transition-all duration-300
              w-full md:w-auto">
                    <i class="fas fa-shopping-basket"></i>
                    Ir al carrito
                </a>
            </div>
            <livewire:resenas-producto :producto="$producto" />
            <div class="mt-8 border-t border-gray-300 dark:border-[#262626] pt-6">
                <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Compartir este producto</h2>

                @php
                    $url = urlencode(url()->current());
                    $titulo = urlencode($producto->nombre);
                    $descripcion = urlencode('¡Mira este producto! Precio: Bs. ' . number_format($producto->precio, 2));
                    $imagen = $producto->imagenes->first() ? $producto->imagenes->first()->ruta : '';
                    $imagenUrl = $imagen ? urlencode(asset($imagen)) : '';
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="https://wa.me/?text={{ $titulo }}%20{{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-green-500/10 dark:bg-green-500/20 
                   border border-green-500/30 dark:border-green-500/40
                   hover:bg-green-500/20 dark:hover:bg-green-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-blue-500/10 dark:bg-blue-500/20 
                   border border-blue-500/30 dark:border-blue-500/40
                   hover:bg-blue-500/20 dark:hover:bg-blue-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url={{ $url }}&media={{ $imagenUrl }}&description={{ $descripcion }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-red-500/10 dark:bg-red-500/20 
                   border border-red-500/30 dark:border-red-500/40
                   hover:bg-red-500/20 dark:hover:bg-red-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-pinterest"></i> Pinterest
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $url }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-blue-800/10 dark:bg-blue-800/20 
                   border border-blue-800/30 dark:border-blue-800/40
                   hover:bg-blue-800/20 dark:hover:bg-blue-800/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $descripcion }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-sky-500/10 dark:bg-sky-500/20 
                   border border-sky-500/30 dark:border-sky-500/40
                   hover:bg-sky-500/20 dark:hover:bg-sky-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-twitter"></i> X (Twitter)
                    </a>
                    <a href="https://t.me/share/url?url={{ $url }}&text={{ $descripcion }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-cyan-500/10 dark:bg-cyan-500/20 
                   border border-cyan-500/30 dark:border-cyan-500/40
                   hover:bg-cyan-500/20 dark:hover:bg-cyan-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-telegram-plane"></i> Telegram
                    </a>
                    <a href="mailto:?subject={{ $titulo }}&body={{ $descripcion }}%20{{ $url }}"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-yellow-500/10 dark:bg-yellow-500/20 
                   border border-yellow-500/30 dark:border-yellow-500/40
                   hover:bg-yellow-500/20 dark:hover:bg-yellow-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                    <a href="https://www.reddit.com/submit?url={{ $url }}&title={{ $titulo }}"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-orange-500/10 dark:bg-orange-500/20 
                   border border-orange-500/30 dark:border-orange-500/40
                   hover:bg-orange-500/20 dark:hover:bg-orange-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fab fa-reddit-alien"></i> Reddit
                    </a>
                    <button onclick="copyLink('{{ url()->current() }}')"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl text-sm 
                   text-gray-800 dark:text-white
                   bg-gray-500/10 dark:bg-gray-500/20 
                   border border-gray-500/30 dark:border-gray-500/40
                   hover:bg-gray-500/20 dark:hover:bg-gray-500/40
                   backdrop-blur-md shadow-md transition-all duration-300">
                        <i class="fas fa-link"></i> Copiar enlace
                    </button>
                </div>
            </div>
            <div id="toast"
                class="hidden fixed bottom-6 right-6 bg-emerald-600 text-white 
                       px-5 py-3 rounded-xl shadow-lg text-sm font-medium 
                       flex items-center gap-2 animate-fade-in">
                ✅ Enlace copiado
            </div>
        </div>
    </div>
    <div id="imageModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50">
        <span class="absolute top-6 right-8 text-white text-3xl cursor-pointer"
            onclick="closeImageModal()">&times;</span>
        <img id="modalImg" src="" class="max-h-[90%] max-w-[90%] rounded-lg shadow-2xl" />
    </div>
    <script>
        function copyLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                let toast = document.getElementById("toast");
                toast.classList.remove("hidden");
                setTimeout(() => toast.classList.add("hidden"), 2000);
            });
        }

        function openImageModal(src) {
            document.getElementById("modalImg").src = src;
            document.getElementById("imageModal").classList.remove("hidden");
            document.getElementById("imageModal").classList.add("flex");
        }

        function closeImageModal() {
            document.getElementById("imageModal").classList.add("hidden");
            document.getElementById("imageModal").classList.remove("flex");
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .share-btn {
            @apply flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200 border transition-all duration-300;
        }
    </style>
</div>
