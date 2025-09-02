<div >
    <h1
        class="text-4xl font-extrabold mb-8 text-center 
        bg-clip-text text-transparent 
        bg-gradient-to-r from-indigo-600 via-blue-500 to-cyan-400
        dark:from-green-400 dark:via-emerald-500 dark:to-teal-400
        drop-shadow-lg">
        Mis Compras
    </h1>

    @if ($ventas->isEmpty())
        <p class="text-center text-gray-600 dark:text-gray-400 text-lg italic">
            Aún no has realizado ninguna compra.
        </p>
    @else
        <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($ventas as $venta)
                <div
                    class="rounded-3xl p-6 shadow-xl
                    bg-white/40 dark:bg-black/30
                    border border-white/20 dark:border-gray-700/40
                    backdrop-blur-2xl
                    transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl
                    animate-fade-in">

                    <div class="flex justify-between items-center mb-4">
                        <span class="font-semibold text-lg text-gray-800 dark:text-gray-200">
                            Venta #{{ $venta->id }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $venta->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="mb-2 text-gray-700 dark:text-gray-300">
                        <span class="font-semibold">Estado:</span>
                        <span class="capitalize">{{ $venta->estado }}</span>
                    </div>

                    <div class="mb-3 text-gray-700 dark:text-gray-300">
                        <span class="font-semibold">Total:</span>
                        <span class="text-green-600 dark:text-green-400 font-bold">
                            {{ number_format($venta->total, 2) }} Bs.
                        </span>
                    </div>

                    <div class="mb-4">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Productos:</span>
                        <ul
                            class="mt-3 space-y-3 max-h-60 overflow-y-auto pr-2 
                            scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">
                            @foreach ($venta->productos as $vp)
                                <li
                                    class="flex items-center gap-4 p-2 rounded-xl
                                    bg-white/40 dark:bg-gray-900/20 
                                    hover:bg-gray-100/70 dark:hover:bg-gray-700/30
                                    backdrop-blur-lg
                                    transition-colors duration-200">
                                    <img src="{{ $vp->producto->imagenes->first()
                                        ? asset($vp->producto->imagenes->first()->ruta)
                                        : 'https://png.pngtree.com/png-vector/20230224/ourmid/pngtree-image-icone-png-image_6617630.png' }}"
                                        class="w-14 h-14 object-cover rounded-lg shadow-md"
                                        alt="{{ $vp->producto->nombre }}">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ $vp->producto->nombre }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Cantidad: {{ $vp->cantidad }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Subtotal: {{ number_format($vp->subtotal, 2) }} Bs.
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-4 text-gray-700 dark:text-gray-300 text-sm">
                        @php
                            $subtotalSum = $venta->productos->sum('subtotal');
                            $total = $venta->total;
                            $diferencia = $subtotalSum - $total;
                            $descuento = $subtotalSum > 0 ? round(($diferencia / $subtotalSum) * 100, 2) : 0;
                        @endphp
                        <span class="font-semibold">Subtotal productos:</span>
                        {{ number_format($subtotalSum, 2) }} Bs. <br>
                        <span class="font-semibold">Total venta:</span>
                        {{ number_format($total, 2) }} Bs.
                        @if ($diferencia > 0)
                            <br>
                            <span class="text-green-600 dark:text-green-400 font-semibold">
                                Descuento aplicado: {{ $descuento }}%
                            </span>
                        @endif
                    </div>
                    @if ($venta->envio)
                        <div
                            class="text-sm text-gray-600 dark:text-gray-400 
                            bg-gray-100/50 dark:bg-gray-900/20 
                            p-4 rounded-xl backdrop-blur-md">
                            <span class="font-semibold">Envío:</span>
                            {{ $venta->envio->direccion }},
                            {{ $venta->envio->ciudad ?? '' }} {{ $venta->envio->departamento ?? '' }},
                            {{ $venta->envio->pais }},
                            CP: {{ $venta->envio->codigo_postal ?? '' }}
                            <br>
                            <span class="font-semibold">Estado envío:</span>
                            {{ ucfirst($venta->envio->estado) }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
@endpush
