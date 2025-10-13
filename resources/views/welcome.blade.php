<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rentas for Movies</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .grain {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 3px 3px;
            opacity: .5;
        }
        .ticket {
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 12px), calc(100% - 16px) 100%, 16px 100%, 0 calc(100% - 12px));
        }
        .glow { box-shadow: 0 10px 25px rgba(99,102,241,.25), inset 0 0 0 1px rgba(255,255,255,.05); }
    </style>
</head>
<body class="h-full bg-[#0b0f17] text-white">

    <div class="absolute inset-0 bg-gradient-to-b from-indigo-900/40 via-[#0b0f17] to-[#0b0f17]"></div>
    <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-indigo-600/20 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-40 h-[600px] w-[600px] rounded-full bg-fuchsia-600/20 blur-3xl"></div>
    <div class="grain"></div>

    <header class="relative">
        <nav class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 font-black tracking-wide">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 7c0-1.1.9-2 2-2h2.59l1-2H17c1.1 0 2 .9 2 2v2H3V7Zm0 2h16v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Zm4 3h6v2H7v-2Z"/>
                </svg>
                <span class="text-xl">Rentas <span class="text-amber-400">for</span> Movies</span>
            </a>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 transition glow">
                        Dashboard
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-white/15 hover:bg-white/5 transition">
                            Iniciar sesión
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-black font-semibold transition glow">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main class="relative">
        <section class="max-w-7xl mx-auto px-6 pt-8 pb-16 md:pt-16 md:pb-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <div class="inline-flex items-center gap-2 text-amber-300/90 text-sm uppercase tracking-wider">
                    <span class="h-1 w-6 bg-amber-400 rounded"></span> Blockbuster vibes
                </div>
                <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight">
                    Tu videoclub <span class="text-amber-400">favorito</span>,<br>
                    versión <span class="text-indigo-400">moderna</span>.
                </h1>
                <p class="mt-4 text-white/80 md:text-lg max-w-prose">
                    Explora el catálogo, renta películas por sucursal, revisa tus pagos y recibe alertas de devolución. Todo en un solo lugar.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    @if (Route::has('customer.catalog'))
                        <a href="{{ route('customer.catalog') }}" class="px-5 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 transition glow">
                            Ver catálogo
                        </a>
                    @endif
                    @guest
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-3 rounded-lg border border-white/15 hover:bg-white/5 transition">
                                Crear cuenta
                            </a>
                        @endif
                    @endguest
                </div>

                <div class="mt-8 flex flex-wrap gap-3 text-xs text-white/70">
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Búsqueda por título, categoría, actor, idioma</span>
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Disponibilidad por sucursal</span>
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">Historial de rentas & pagos</span>
                </div>
            </div>

            <div class="relative">
                <div class="ticket bg-gradient-to-br from-indigo-700/40 to-fuchsia-700/30 border border-white/10 rounded-xl p-6 md:p-8 shadow-2xl backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 7c0-1.1.9-2 2-2h2.59l1-2H17c1.1 0 2 .9 2 2v2H3V7Zm0 2h16v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Zm4 3h6v2H7v-2Z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-white/70">Pase Premium</p>
                                <p class="font-bold">Rentas for Movies</p>
                            </div>
                        </div>
                        <span class="text-amber-400 font-semibold">★ ★ ★ ★ ★</span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-black/20 border border-white/10 rounded-lg p-4">
                            <p class="text-white/60">Catálogo</p>
                            <p class="font-semibold">+1,000 títulos</p>
                        </div>
                        <div class="bg-black/20 border border-white/10 rounded-lg p-4">
                            <p class="text-white/60">Sucursales</p>
                            <p class="font-semibold">Inventario en tiempo real</p>
                        </div>
                        <div class="bg-black/20 border border-white/10 rounded-lg p-4">
                            <p class="text-white/60">Rentas</p>
                            <p class="font-semibold">Historial & Devoluciones</p>
                        </div>
                        <div class="bg-black/20 border border-white/10 rounded-lg p-4">
                            <p class="text-white/60">Pagos</p>
                            <p class="font-semibold">Cargos & Recibos</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between text-xs text-white/60">
                        <span>Member ID: 0001</span>
                        <span>Valid: {{ now()->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative">
        <div class="max-w-7xl mx-auto px-6 pb-10 text-center text-white/60 text-sm">
            © {{ date('Y') }} Rentas for Movies. Hecho con Laravel.
        </div>
    </footer>
</body>
</html>
