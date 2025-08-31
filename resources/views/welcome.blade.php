<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>University Marketplace</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] antialiased">

    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto px-6 lg:px-8 flex justify-between items-center py-4">
        <div class="text-2xl font-semibold text-green-600">Tu ex marketplace</div>
        <nav class="flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 border border-green-600 text-green-600 rounded hover:bg-green-50 transition">Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </header>

    <!-- Hero Section -->
    <section class=" w-full max-w-7xl mx-auto px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between py-16 gap-8 ">
        <div class="lg:w-1/2">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4 text-[#1b1b18] dark:text-white">
                Bienvenido a Tu ex marketplace
            </h1>
            <p class="text-lg text-zinc-700 dark:text-zinc-400 mb-6">
                El marketplace universitario donde emprendedores muestran sus productos y servicios. Compra y apoya a estudiantes como tú.
            </p>
        </div>
        <div class="">
            <img src="https://static.vecteezy.com/system/resources/previews/008/952/123/non_2x/logo-template-for-green-flower-shop-free-vector.jpg" alt="Marketplace" class="rounded-xl shadow-lg max-w-[200px] lg:max-w-[300px]">
        </div>
    </section>

    <!-- Productos Destacados -->
    <section id="productos" class="w-full max-w-7xl mx-auto px-6 lg:px-8 py-12 ">
    <livewire:productos />
    </section>

    <!-- Footer -->
    <footer class="w-full bg-[#1b1b18] dark:bg-zinc-900 text-white py-8 mt-12">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 flex flex-col lg:flex-row justify-between items-center">
            <span>&copy; {{ date('Y') }} Tu ex marketplace. Todos los derechos reservados.</span>
            <div class="flex gap-4 mt-4 lg:mt-0">
                <a href="#" class="hover:text-green-500 transition">Contacto</a>
                <a href="#" class="hover:text-green-500 transition">Sobre nosotros</a>
                <a href="#" class="hover:text-green-500 transition">Términos</a>
            </div>
        </div>
    </footer>

</body>
</html>
