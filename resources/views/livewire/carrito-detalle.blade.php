<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Mi carrito</h1>

    <div class="bg-white dark:bg-[#171717] rounded-xl shadow-lg p-4">
        @forelse($items as $item)
            <div class="flex justify-between items-center border-b py-4">
                <div class="flex items-center gap-4">
                    <img src="{{ $item->imagenPrincipal }}" alt="Imagen del producto"
                         class="w-20 h-20 object-cover rounded-lg">

                    <div>
                        <p class="font-semibold">{{ $item->producto->nombre }}</p>
                        <p class="text-sm text-gray-500">Agregado: {{ $item->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm text-gray-400">
                            Precio: Bs. {{ number_format($item->producto->precio, 2) }} |
                            Stock: {{ $item->producto->stock }}
                        </p>
                        @if($item->promedioResena)
                            <p class="text-yellow-400">
                                ⭐ {{ number_format($item->promedioResena, 1) }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="number" min="1"
                        wire:change="actualizarCantidad({{ $item->id }}, $event.target.value)"
                        value="{{ $item->cantidad }}"
                        class="w-16 border rounded p-1 text-center dark:bg-gray-800 dark:text-white" />
                    <p class="font-semibold">Bs. {{ number_format($item->subtotal, 2) }}</p>
                    <button wire:click="eliminar({{ $item->id }})"
                        class="text-red-500 hover:text-red-700">X</button>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No tienes productos en tu carrito.</p>
        @endforelse

        <div class="mt-6 text-right font-bold">
            Total: Bs. {{ number_format($total, 2) }}
        </div>
    </div>
</div>
