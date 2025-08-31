<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white p-6">

    <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Bolsa -->
        <div class="lg:col-span-2">
            <flux:button href="{{ route('dashboard') }}">
                Volver
            </flux:button>


            @if($productos->count() > 0)
            @foreach($productos as $item)
            @php
            $producto = $item->producto;
            $cantidad = $item->cantidad;
            $subtotal = $item->subtotal;
            @endphp

            <div class="flex flex-col sm:flex-row items-start border-b border-gray-200 pb-6 mb-6">
                <!-- Imagen -->
                <div class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                    @if($producto->imagenes->isNotEmpty())
                    <img src="{{ $producto->imagenes->first()->ruta }}"
                        class="w-full h-full object-contain rounded-lg"
                        alt="{{ $producto->nombre }}">
                    @else
                    <img src="https://via.placeholder.com/150"
                        class="w-full h-full object-contain rounded-lg"
                        alt="{{ $producto->nombre }}">
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 ml-0 sm:ml-6 mt-4 sm:mt-0 space-y-2">
                    <h3 class="text-lg font-semibold">{{ $producto->nombre }}</h3>
                    <p class="text-gray-500 text-sm">{{ $producto->descripcion ?? 'Descripción del producto' }}</p>
                    <p class="text-black font-bold">{{ number_format($producto->precio,2) }} Bs</p>

                    <!-- Controles -->
                    <div class="flex items-center space-x-4 mt-2">
                        <!-- Botón eliminar -->
                        <form action="{{ route('carrito.eliminar', $producto->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-black">
                                🗑️
                            </button>
                        </form>

                        <!-- Cantidad -->
                        <form action="{{ route('carrito.actualizar', $producto->id) }}" method="POST" class="flex items-center border rounded">
                            @csrf
                            <button type="submit" name="cantidad" value="{{ $cantidad - 1 }}" class="px-3 py-1">-</button>
                            <input type="text" value="{{ $cantidad }}" class="w-10 text-center border-l border-r" readonly>
                            <button type="submit" name="cantidad" value="{{ $cantidad + 1 }}" class="px-3 py-1">+</button>
                        </form>

                        <!-- Favorito -->
                        <button class="text-gray-400 hover:text-red-500">♡</button>
                    </div>
                </div>

                <!-- Subtotal a la derecha -->
                <div class="ml-auto mt-4 sm:mt-0 font-semibold text-black">
                    {{ number_format($subtotal,2) }} Bs
                </div>
            </div>
            @endforeach
            @else
            <p>Tu carrito está vacío 😢</p>
            @endif
        </div>

        <!-- Resumen -->
        <div class="bg-white rounded-xl shadow p-6 h-fit border border-gray-200">
            <h2 class="text-xl font-bold mb-6">Resumen</h2>

            <!-- Campo de cupón -->
            <div class="mb-6">
                <label for="coupon" class="block text-sm font-medium text-gray-700 mb-2">¿Tienes un código de descuento?</label>
                <div class="flex">
                    <input type="text" id="coupon" name="coupon"
                        class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        placeholder="Ingresa tu cupón">
                    <button type="button"
                        class="bg-black text-white px-4 rounded-r-lg hover:bg-gray-800">
                        Aplicar
                    </button>
                </div>
                @if(session('coupon_message'))
                <p class="text-sm text-green-600 mt-2">{{ session('coupon_message') }}</p>
                @endif
            </div>

            <div class="flex justify-between mb-4 text-gray-600">
                <span>Subtotal</span>
                <span>{{ number_format($carrito->total ?? 0,2) }} Bs</span>
            </div>

            <div class="flex justify-between mb-4 text-gray-600">
                <span>Envío estimado</span>
                <span class="text-green-600 font-semibold">Gratis</span>
            </div>

            <div class="flex justify-between mb-6 text-gray-600">
                <span>Impuestos estimados</span>
                <span>—</span>
            </div>

            <div class="flex justify-between font-bold text-lg border-t border-gray-200 pt-4">
                <span>Total</span>
                <span>{{ number_format($total ?? 0,2) }} Bs</span>
            </div>

            <button class="w-full bg-black text-white py-3 rounded-lg mt-6 hover:bg-gray-800 font-medium">Finalizar compra</button>
            <button class="w-full border border-gray-400 text-blue-600 py-3 rounded-lg mt-3 hover:bg-gray-100 font-medium">PayPal</button>
        </div>


</body>

</html>