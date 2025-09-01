<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tu ex Market</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
    <script src="https://arkdev.pages.dev/arkdev.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="text-white min-h-screen flex flex-col overflow-x-hidden relative font-sans">
    <div id="animated-bg"
        class="absolute inset-0 -z-10 bg-gradient-to-br from-black via-gray-800 to-green-900 bg-[length:200%_200%]">
    </div>
    <div id="crystals" class="absolute inset-0 -z-5 overflow-hidden"></div>
    <header class="relative w-full h-[97vh] flex flex-col justify-center items-center text-center overflow-hidden">
        <img src="https://resizer.glanacion.com/resizer/v2/son-latinos-y-revelan-cuatro-consejos-para-4UZZVK4KS5BTJDGFQKC5BBTX5Y.jpg?auth=2cfd448476d30465c93dceb66f4bf0a3e6af515ecefd8bc6aa1956828707de9c&width=768&quality=70&smart=false"
            alt="Hero mobile" class="absolute inset-0 w-full h-full object-cover -z-10 block md:hidden">
        <img src="https://universidadeuropea.com/resources/media/images/tipos-de-emprendedores-800x450.original.jpg"
            alt="Hero desktop" class="absolute inset-0 w-full h-full object-cover -z-10 hidden md:block">
        <div class="absolute inset-0 bg-black/60 -z-10"></div>
        <div class="px-6 lg:px-16 max-w-4xl">
            <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-snug">
                El marketplace donde los <span class="text-green-400">emprendedores brillan</span>
                y tus compras <span class="text-green-300">cobran vida</span>
            </h1>
            <p id="fun-text" class="text-lg md:text-2xl font-medium text-gray-200 h-12"></p>
        </div>
        <div class="absolute bottom-8 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <nav
            class="absolute top-0 left-0 w-full flex justify-between items-center px-6 lg:px-16 py-4 bg-black/40 backdrop-blur-md">
            <h2 class="text-2xl font-bold text-white">Tu ex Market</h2>
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition-all">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 text-green-400"></i>
                            Panel de control
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition-all">
                            <i data-lucide="log-in" class="w-5 h-5 text-blue-400"></i>
                            Iniciar sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition-all">
                                <i data-lucide="user-plus" class="w-5 h-5 text-pink-400"></i>
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </header>
    <main class="flex-grow w-full min-h-screen flex justify-center items-center px-6 lg:px-16 py-10">
        @livewire('mostrar-welcome')
    </main>
    <footer class="w-full py-8 bg-black/70 text-gray-300 flex flex-col items-center gap-2 backdrop-blur-md">
        <p class="text-sm">© 2025 Tu ex Market. Todos los derechos reservados.</p>
        <p id="credit-text"
            class="text-xs opacity-70 text-center transition-all duration-500 hover:opacity-100 hover:text-green-400">
            Diseño moderno y elegante con <span class="font-semibold">GSAP</span>, <span class="font-semibold">Tailwind
                CSS</span>, <span class="font-semibold">Laravel 12</span> y <span class="font-semibold">Livewire
                v3</span>
        </p>
        <p class="text-xs mt-2">Powered by <a href="https://arkdev.pages.dev/nosotros"
                class="text-green-400 hover:underline">ARK DEV SYSTEM</a></p>
    </footer>
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from("header h1", {
                y: -50,
                opacity: 0,
                duration: 1,
                ease: "power3.out"
            });
            gsap.from("header p", {
                opacity: 0,
                y: 30,
                duration: 1.2,
                delay: 0.3,
                ease: "power3.out"
            });
            gsap.from("nav", {
                y: -60,
                opacity: 0,
                duration: 1,
                delay: 0.2,
                ease: "power3.out"
            });
            gsap.from("footer", {
                opacity: 0,
                y: 50,
                duration: 1,
                delay: 0.5,
                ease: "power3.out"
            });
            gsap.to("#animated-bg", {
                backgroundPosition: "200% 200%",
                duration: 27,
                ease: "sine.inOut",
                repeat: -1,
                yoyo: true
            });
            const crystalsContainer = document.getElementById('crystals');
            const crystalCount = 30;
            for (let i = 0; i < crystalCount; i++) {
                const crystal = document.createElement('div');
                crystal.classList.add('absolute', 'rounded-xl', 'bg-white/10', 'border', 'border-white/20',
                    'shadow-lg');
                const size = Math.random() * 60 + 20;
                crystal.style.width = `${size}px`;
                crystal.style.height = `${size}px`;
                crystal.style.top = `${Math.random() * 100}%`;
                crystal.style.left = `${Math.random() * 100}%`;
                crystal.style.transform = `rotate(${Math.random() * 360}deg)`;
                crystal.style.opacity = Math.random() * 0.4 + 0.1;
                crystalsContainer.appendChild(crystal);
                gsap.to(crystal, {
                    y: '+=50',
                    x: '+=30',
                    rotation: Math.random() * 360,
                    duration: Math.random() * 10 + 10,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    delay: Math.random() * 5
                });
            }
            const phrases = [
                "¡Bienvenido al lugar donde tu ex vendía productos y tú ahora eres VIP!",
                "Aquí tus compras son más dulces que su arrepentimiento 😎",
                "Antes era su tienda... ahora es tu playground de ofertas 😏",
                "Tu ex Market: comprando cosas y robando sonrisas desde 2025"
            ];
            let i = 0,
                j = 0;
            const funText = document.getElementById('fun-text');

            function typeWriter() {
                if (j < phrases[i].length) {
                    funText.textContent += phrases[i][j];
                    j++;
                    setTimeout(typeWriter, 50);
                } else {
                    setTimeout(() => {
                        funText.textContent = '';
                        j = 0;
                        i = (i + 1) % phrases.length;
                        typeWriter();
                    }, 4000);
                }
            }
            typeWriter();
        });
    </script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
