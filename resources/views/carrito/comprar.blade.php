<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sección Productos (Bag) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-6 space-y-6">

            <h2 class="text-2xl font-bold mb-4">Tu Carrito</h2>

            @if($productos->count() > 0)
            @foreach($productos as $item)
            @php
            $producto = $item->producto; // relación con productos
            $cantidad = $item->cantidad;
            $subtotal = $item->subtotal;
            @endphp

            <div class="flex flex-col sm:flex-row items-center border-b border-gray-200 pb-4 mb-4">
                <!-- Imagen -->
                <div class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
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

                <!-- Info -->
                <div class="flex-1 ml-0 sm:ml-6 mt-4 sm:mt-0">
                    <h3 class="text-lg font-semibold text-black">{{ $producto->nombre }}</h3>
                    <p class="text-gray-500 text-sm">{{ $producto->descripcion ?? 'Descripción del producto' }}</p>
                    <p class="text-gray-700 font-bold mt-2">{{ number_format($producto->precio,2) }} Bs</p>

                    <!-- Controles de cantidad -->
                    <form action="{{ route('carrito.actualizar', $producto->id) }}" method="POST" class="flex items-center mt-2 space-x-2">
                        @csrf
                        <button type="submit" name="cantidad" value="{{ $cantidad - 1 }}"
                            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">-</button>
                        <input type="text" value="{{ $cantidad }}"
                            class="w-12 text-center border rounded" readonly>
                        <button type="submit" name="cantidad" value="{{ $cantidad + 1 }}"
                            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
                    </form>

                    <!-- Botón eliminar -->
                    <form action="{{ route('carrito.eliminar', $producto->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline text-sm">Eliminar</button>
                    </form>
                </div>
            </div>
            @endforeach
            @else
            <p>Tu carrito está vacío 😢</p>
            @endif

        </div>

        <!-- Sección Resumen -->
        <div class="bg-white rounded-xl shadow p-6 h-fit">
            <h2 class="text-xl font-bold mb-4">Resumen de Compra</h2>

            <div class="flex justify-between mb-2">
                <span>Subtotal</span>
                <span>{{ number_format($carrito->total ?? 0,2) }} Bs</span>

            </div>

            <div class="flex justify-between mb-2">
                <span>Envío estimado</span>
                <span class="text-green-600 font-semibold">Gratis</span>
            </div>

            <div class="flex justify-between mb-4">
                <span>Impuestos estimados</span>
                <span>—</span>
            </div>

            <div class="flex justify-between font-bold text-lg border-t border-gray-200 pt-2">
                <span>Total</span>
                <span>{{ number_format($total ?? 0,2) }} Bs</span>
            </div>

            <button class="w-full bg-black text-white py-3 rounded-lg mt-4 hover:bg-gray-800">Checkout</button>
            <button class="w-full border border-gray-400 text-blue-600 py-3 rounded-lg mt-2 hover:bg-gray-100">PayPal</button>
        </div>

    </div>

</body>

</html>