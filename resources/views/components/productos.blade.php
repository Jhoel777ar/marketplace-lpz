@props(['productos'])
    @foreach($productos as $producto)
        <div class="bg-white rounded-xl shadow p-4 relative flex flex-col">
            <h3 class="text-lg font-medium text-black mb-2">{{ $producto->nombre }}</h3>
            <div class="w-full aspect-[16/9] flex items-center justify-center rounded-lg overflow-hidden mb-2">
                @if($producto->imagenes->isNotEmpty())
                    <img src="{{ $producto->imagenes->first()->ruta }}" class="w-full h-full object-contain"
                        alt="{{ $producto->nombre }}">
                @else
                    <img src="https://via.placeholder.com/150" class="w-full h-full object-contain"
                        alt="{{ $producto->nombre }}">
                @endif
            </div>
            <div class="flex mt-auto">
                <form action="{{ route('carrito.agregar', $producto->id) }}" method="POST" class="agregar-carrito w-full">
                    @csrf
                    <button type="submit"
                        class="w-1/5 bg-black text-white py-2 rounded-lg flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5-9v9m-4-9v9" />
                        </svg>
                    </button>
                </form>
                <p class="text-gray-600 font-bold text-right mb-3">{{ number_format($producto->precio, 2) }} Bs</p>
            </div>
        </div>
    @endforeach

<script>
document.addEventListener("submit", async (e) => {
    const form = e.target.closest('.agregar-carrito');
    if (!form) return; // Ignorar otros formularios
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
        const carritoIcon = document.getElementById("carrito-icon");
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
</script>
