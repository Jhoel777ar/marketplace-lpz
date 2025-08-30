<div class="mt-6">
    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Reseñas</h2>
    @auth
        <form wire:submit.prevent="submit"
            class="space-y-4 mb-8 bg-gray-50 dark:bg-[#1a1a1a] p-4 rounded-2xl shadow-md transition">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Calificación producto
                </label>
                <div class="flex space-x-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('calificacion_producto', {{ $i }})"
                            class="text-2xl transition transform duration-150 ease-in-out
                            {{ $calificacion_producto >= $i
                                ? 'text-yellow-400 hover:text-yellow-500 scale-110'
                                : 'text-gray-300 dark:text-gray-600 hover:text-yellow-300 hover:scale-110' }}">
                            ★
                        </button>
                    @endfor
                </div>
                <p class="text-xs text-gray-500 mt-1 font-semibold">
                    {{ $calificacion_producto }}/5
                </p>
                @error('calificacion_producto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Calificación servicio (opcional)
                </label>
                <div class="flex space-x-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('calificacion_servicio', {{ $i }})"
                            class="text-2xl transition transform duration-150 ease-in-out
                            {{ $calificacion_servicio >= $i
                                ? 'text-blue-400 hover:text-blue-500 scale-110'
                                : 'text-gray-300 dark:text-gray-600 hover:text-blue-300 hover:scale-110' }}">
                            ★
                        </button>
                    @endfor
                </div>
                @if ($calificacion_servicio)
                    <p class="text-xs text-gray-500 mt-1 font-semibold">
                        {{ $calificacion_servicio }}/5
                    </p>
                @endif
                @error('calificacion_servicio')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Reseña
                </label>
                <textarea wire:model="reseña" rows="3"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-[#171717] focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                @error('reseña')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full md:w-auto px-6 py-2.5 rounded-2xl font-semibold 
           text-gray-800 dark:text-white 
           bg-emerald-500/10 dark:bg-emerald-500/20 
           border border-emerald-500/30 dark:border-emerald-500/40 
           hover:bg-emerald-500/20 dark:hover:bg-emerald-500/40 
           backdrop-blur-md shadow-md transition-all duration-300">
                {{ $editingResenaId ? 'Editar reseña' : 'Enviar reseña' }}
            </button>
        </form>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Inicia sesión para dejar una reseña.</p>
    @endauth
    @forelse($resenas as $resena)
        <div
            class="mt-4 p-5 border border-gray-200 dark:border-[#262626] rounded-2xl shadow-sm hover:shadow-md transition relative">

            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ $resena->usuario->name }}
            </p>
            <div class="mt-1 flex items-center gap-2">
                <span class="text-yellow-500 font-medium">⭐ {{ $resena->calificacion_producto }}/5</span>
                <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500" style="width: {{ ($resena->calificacion_producto / 5) * 100 }}%">
                    </div>
                </div>
            </div>
            @if ($resena->calificacion_servicio)
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-blue-500 font-medium">🛎 {{ $resena->calificacion_servicio }}/5</span>
                    <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500"
                            style="width: {{ ($resena->calificacion_servicio / 5) * 100 }}%"></div>
                    </div>
                </div>
            @endif
            <p class="mt-2 text-gray-600 dark:text-gray-400 leading-relaxed">
                {{ $resena->reseña }}
            </p>
            @if (Auth::id() === $resena->user_id)
                <div class="absolute top-3 right-3 flex gap-2">
                    <button wire:click="edit({{ $resena->id }})"
                        class="text-sm px-3 py-1 rounded-2xl font-semibold
           text-gray-800 dark:text-white
           bg-yellow-400/20 dark:bg-yellow-400/30
           border border-yellow-400/30 dark:border-yellow-400/50
           hover:bg-yellow-400/30 dark:hover:bg-yellow-400/50
           backdrop-blur-md shadow-md transition-all duration-300">
                        Editar
                    </button>

                    <button wire:click="delete({{ $resena->id }})"
                        class="text-sm px-3 py-1 rounded-2xl font-semibold
           text-gray-800 dark:text-white
           bg-red-500/20 dark:bg-red-500/30
           border border-red-500/30 dark:border-red-500/50
           hover:bg-red-500/30 dark:hover:bg-red-500/50
           backdrop-blur-md shadow-md transition-all duration-300">
                        Eliminar
                    </button>
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">Este producto no tiene reseñas aún.</p>
    @endforelse

    <div class="mt-6">
        {{ $resenas->links() }}
    </div>
</div>
