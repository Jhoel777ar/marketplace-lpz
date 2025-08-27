<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div
            class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
            <div class="absolute inset-0 overflow-hidden">
                <img src="https://www.genwords.com/wp-content/uploads/2018/08/68-scaled-1.jpg" alt="Imagen de fondo"
                    class="object-cover w-full h-full filter blur-sm brightness-90 transition-all duration-500
                    dark:brightness-50 dark:contrast-125" />
                <div
                    class="absolute inset-0
                    bg-white/20 dark:bg-black/40
                    backdrop-blur-sm transition-colors duration-500">
                </div>
            </div>
            <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                <span class="flex h-10 w-10 items-center justify-center rounded-md">
                    <x-app-logo-icon class="me-2 h-7 fill-current text-white dark:text-white" />
                </span>
                {{ config('app.name', 'Laravel') }}
            </a>
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2 text-center">
                    <flux:heading size="lg" class="text-white dark:text-white drop-shadow-lg">
                        Tu ex Market
                    </flux:heading>
                    <footer>
                        <flux:heading class="text-white/80 dark:text-white/90 drop-shadow-md">
                            “La simplicidad es la esencia de la felicidad.” – Cedric Bledsoe
                        </flux:heading>
                    </footer>
                </blockquote>
            </div>
        </div>

        <div class="w-full lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden"
                    wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
