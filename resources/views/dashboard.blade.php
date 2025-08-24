<x-layouts.app :title="__('Dashboard')">
</x-layouts.app>
<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div
        class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">

        <!-- Header -->
        <header class="shadow">
            <div class=" mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="text-lg font-bold">LOGO</div>
                    <form id="searchForm" action="{{ route('inicio') }}" method="GET"
                        class="flex items-center space-x-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..."
                            class="px-3 py-1 border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
                            id="searchInput">
                    </form>
                </div>
            </div>
        </header>

        <!-- Banner conferencia -->
        <section class="relative bg-white text-white overflow-hidden">
            <div class="mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 items-center relative">

                <!-- Texto -->
                <div>
                    <h1 class="text-4xl font-extrabold uppercase text-black">
                        Bienvenido a <br>
                        <span class="text-black-200">Tu Ex Market</span>
                    </h1>
                    <p class="mt-4 text-black">
                        El marketplace creado para emprendedores universitarios.
                        Compra, vende e impulsa tus ideas en una comunidad conectada.
                    </p>
                    <p class="mt-3 text-gray-400 font-semibold">
                        Conecta · Emprende · Crece
                    </p>
                </div>

                <!-- Imagen / carrito -->
                <div class="flex justify-center relative">
                    <div class="w-64 h-64 bg-gray-400 rounded-xl flex items-center justify-center">
                        <span class="text-gray-800 font-bold">LOGO</span>
                    </div>

                    <!-- Icono carrito -->
                    <a href="{{ route('carrito.ver') }}" class="relative" id="carrito-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-black" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5-9v9m-4-9v9" />
                        </svg>

                        @php
                        $contador = app(\App\Http\Controllers\CarritoController::class)->contador();
                        @endphp
                        @if($contador > 0)
                        <span id="contador-carrito"
                            class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $contador }}
                        </span>
                        @endif
                    </a>
                </div>

            </div>
        </section>

        <main class="mx-auto px-6 py-10 grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Productos -->
            <section class="col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($productos as $producto)
                <div class="bg-white rounded-xl shadow p-4 relative flex flex-col">

                    <h3 class="text-lg font-medium text-black mb-2">{{ $producto->nombre }}</h3>

                    <div class="w-full aspect-[16/9] bg-gray-100 flex items-center justify-center rounded-lg overflow-hidden mb-2">
                        @if($producto->imagenes->isNotEmpty())
                        <img src="{{ $producto->imagenes->first()->ruta }}"
                            class="w-full h-full object-contain"
                            alt="{{ $producto->nombre }}">
                        @else
                        <img src="https://via.placeholder.com/150"
                            class="w-full h-full object-contain"
                            alt="{{ $producto->nombre }}">
                        @endif
                    </div>

                    <p class="text-gray-600 font-bold text-right mb-3">
                        {{ number_format($producto->precio, 2) }} Bs
                    </p>

                    <div class="flex space-x-2 mt-auto">
                        <!-- Agregar al carrito -->
                        <form action="{{ route('carrito.agregar', $producto->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full bg-black text-white py-2 rounded-lg flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5-9v9m-4-9v9" />
                                </svg>
                            </button>
                        </form>

                        <!-- Botón Comprar -->
                        <button class="flex-1 w-full bg-black text-white py-2 rounded-lg flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                            </svg>
                        </button>
                    </div>

                </div>
                @endforeach
            </section>

            <!-- Categorías -->
            <aside class="col-span-1">
                <ul class="space-y-2">
                    @foreach (['Categoria 1', 'Categoria 2', 'Categoria 3', 'Categoria 4', 'Categoria 5'] as $cat)
                    <li class="bg-white rounded-lg shadow p-3 hover:bg-gray-100 cursor-pointer text-black">
                        {{ $cat }}
                    </li>
                    @endforeach
                </ul>
            </aside>

        </main>

        <script>
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            searchInput.addEventListener('input', function() {
                searchForm.submit();
            });

            document.addEventListener("DOMContentLoaded", () => {
                const carritoIcon = document.getElementById("carrito-icon");

                document.querySelectorAll("form[action*='carrito/agregar']").forEach(form => {
                    form.addEventListener("submit", async (e) => {
                        e.preventDefault();

                        let url = form.action;
                        let token = form.querySelector('input[name="_token"]').value;

                        let response = await fetch(url, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": token,
                                "Accept": "application/json"
                            }
                        });

                        if (response.ok) {
                            let data = await response.json();
                            let contador = document.getElementById("contador-carrito");

                            if (contador) {
                                contador.textContent = data.cantidad;
                            } else if (carritoIcon) {
                                let span = document.createElement('span');
                                span.id = "contador-carrito";
                                span.className = "absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center z-50";
                                span.textContent = data.cantidad;
                                carritoIcon.appendChild(span);
                            }
                        }
                    });
                });
            });
        </script>

    </div>
</div>
