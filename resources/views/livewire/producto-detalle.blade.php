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
            <div class="mt-6">
                <h2 class="text-lg font-bold">Compartir producto</h2>

                @php
                    // Datos que se comparten — solo datos autorizados
                    $productoNombre = $producto->nombre;
                    $productoPrecio = number_format($producto->precio, 2);
                    $sitio = config('app.name', 'Marketplace');
                    $productoUrl = request()->fullUrl();
                    $imagen = $producto->imagenes->first()->ruta ?? '';
                    $textoCompartir = "Mira este producto: {$productoNombre} - Bs. {$productoPrecio} en {$sitio}";
                @endphp

                <div class="flex flex-wrap gap-3 mt-3 items-center">
                    <a target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($productoUrl) }}&amp;quote={{ urlencode($textoCompartir) }}" title="Compartir en Facebook" aria-label="Compartir en Facebook" class="w-10 h-10 flex items-center justify-center bg-[#1877F2] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fab fa-facebook-f"></i>
                        <span class="sr-only">Compartir en Facebook</span>
                    </a>

                    <a target="_blank" rel="noopener noreferrer" href="https://api.whatsapp.com/send?phone=59168082113&amp;text={{ urlencode($textoCompartir . ' ' . $productoUrl) }}" title="Enviar por WhatsApp (+591 68082113)" aria-label="Enviar por WhatsApp (+591 68082113)" class="w-10 h-10 flex items-center justify-center bg-[#25D366] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fab fa-whatsapp"></i>
                        <span class="sr-only">Enviar por WhatsApp</span>
                    </a>

                    <a target="_blank" rel="noopener noreferrer" href="https://twitter.com/intent/tweet?text={{ urlencode($textoCompartir) }}&amp;url={{ urlencode($productoUrl) }}" title="Compartir en X (Twitter)" aria-label="Compartir en X (Twitter)" class="w-10 h-10 flex items-center justify-center bg-[#1DA1F2] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fab fa-x-twitter"></i>
                        <span class="sr-only">Compartir en X</span>
                    </a>

                    <a target="_blank" rel="noopener noreferrer" href="https://t.me/share/url?url={{ urlencode($productoUrl) }}&amp;text={{ urlencode($textoCompartir) }}" title="Compartir en Telegram" aria-label="Compartir en Telegram" class="w-10 h-10 flex items-center justify-center bg-[#2AABEE] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fab fa-telegram-plane"></i>
                        <span class="sr-only">Compartir en Telegram</span>
                    </a>

                    <a target="_blank" rel="noopener noreferrer" href="https://www.pinterest.com/pin/create/button/?url={{ urlencode($productoUrl) }}&amp;media={{ urlencode($imagen) }}&amp;description={{ urlencode($textoCompartir) }}" title="Compartir en Pinterest" aria-label="Compartir en Pinterest" class="w-10 h-10 flex items-center justify-center bg-[#BD081C] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fab fa-pinterest-p"></i>
                        <span class="sr-only">Compartir en Pinterest</span>
                    </a>

                    <!-- Instagram: copiar enlace (solo icono) -->
                    <button type="button" onclick="copiarEnlaceCompartir()" title="Copiar enlace para Instagram" aria-label="Copiar enlace para Instagram" class="w-10 h-10 flex items-center justify-center bg-[#E4405F] hover:opacity-90 rounded-full text-white shadow">
                        <i class="fas fa-link"></i>
                        <span class="sr-only">Copiar enlace</span>
                    </button>
                </div>

                <p class="text-xs text-gray-400 mt-2">Privacidad: Solo datos autorizados se comparten. Se compartirá únicamente: nombre del producto, precio, imagen pública (si existe) y el enlace público al producto.</p>

                <input type="text" id="enlaceCompartir" value="{{ $productoUrl }}" readonly class="sr-only" />

                <script>
                    function copiarEnlaceCompartir() {
                        const input = document.getElementById('enlaceCompartir');
                        input.select ? input.select() : null;
                        input.setSelectionRange ? input.setSelectionRange(0, 99999) : null;
                        try {
                            document.execCommand('copy');
                        } catch (e) {
                            // fallback for browsers that block execCommand
                        }
                        // As a friendly feedback, show a small toast
                        const toast = document.createElement('div');
                        toast.textContent = 'Enlace copiado al portapapeles. Pega en Instagram o compártelo.';
                        toast.className = 'fixed bottom-6 right-6 bg-[#171717] text-white px-4 py-2 rounded shadow-lg';
                        document.body.appendChild(toast);
                        setTimeout(() => document.body.removeChild(toast), 3000);
                    }
                </script>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</div>
