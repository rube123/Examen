@php
    $user  = auth()->user();
    $role  = optional($user->role)->name;
    $labels = [
        'admin'    => 'Administrador',
        'employee' => 'Empleado de sucursal',
        'customer' => 'Cliente',
        'public'   => 'Público general',
    ];
    $label = $labels[$role] ?? 'Sin rol';

    if (!isset($stores)) {
        $stores = \DB::table('store')->select('store_id')->orderBy('store_id')->get();
    }
    if (!isset($languages)) {
        $languages = \DB::table('language')->select('language_id','name')->orderBy('name')->get();
    }
    if (!isset($categories)) {
        $categories = \DB::table('category')->select('category_id','name')->orderBy('name')->get();
    }

    $hasNotificationsTable = \Schema::hasTable('notifications');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-yellow-400 tracking-wide">
            🎬 Blockbuster Dashboard
        </h2>
        <p class="text-blue-300 text-sm">Bienvenido al panel principal</p>
    </x-slot>

    <style>
        body { background: #0b0f17 !important; }
        .blockbuster-bg {
            background: linear-gradient(180deg, #0b0f17 0%, #101622 100%);
            min-height: 100vh;
            color: white;
            position: relative;
        }
        .blockbuster-card {
            background: linear-gradient(135deg, #141c2e, #1d2640);
            border: 2px solid #facc15;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: transform 0.3s;
        }
        .blockbuster-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
        }
        .blockbuster-btn {
            background: #2563eb;
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            font-weight: bold;
            transition: background 0.3s;
        }
        .blockbuster-btn:hover {
            background: #1d4ed8;
        }
        .pill {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8rem;
        }

        /* 👇 Solución para selects */
        select, select option {
            background-color: #0b0f17 !important;
            color: #ffffff !important;
        }
        select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(250, 204, 21, 0.7);
        }
    </style>

    <div class="blockbuster-bg py-10 px-6">
        <div class="max-w-7xl mx-auto">

            <h1 class="text-4xl font-extrabold text-yellow-400 mb-2">🎥 Bienvenido, {{ $user->name }}</h1>
            <p class="text-blue-300 mb-10">Rol: <span class="font-bold text-yellow-300">{{ $label }}</span></p>

            {{-- ALERTAS --}}
            @if($role === 'customer' && $hasNotificationsTable && method_exists($user, 'unreadNotifications') && $user->unreadNotifications->count())
                <div class="blockbuster-card mb-10">
                    <h2 class="text-2xl font-semibold text-yellow-400 mb-3">⚠️ Alertas importantes</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        @foreach($user->unreadNotifications as $n)
                            <li>
                                {{ $n->data['message'] ?? 'Tienes una notificación de tu renta.' }}
                                <span class="text-sm text-yellow-200">· {{ $n->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ACCESOS RÁPIDOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                <a href="{{ route('customer.catalog') }}" class="blockbuster-card text-center text-yellow-300">🎞️ Catálogo</a>
                <a href="{{ route('customer.rentals') }}" class="blockbuster-card text-center text-yellow-300">📦 Mis rentas</a>
                <a href="{{ route('customer.payments') }}" class="blockbuster-card text-center text-yellow-300">💳 Pagos</a>
                <a href="{{ route('customer.charges') }}" class="blockbuster-card text-center text-yellow-300">📑 Cargos</a>
            </div>

            {{-- BUSCADOR --}}
            <div class="blockbuster-card mb-10">
                <h2 class="text-2xl font-semibold text-yellow-400 mb-4">🔍 Buscar Películas</h2>
                <form method="GET" action="{{ route('customer.catalog') }}" class="grid md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1 text-yellow-200">Título</label>
                        <input type="text" name="title" value="{{ request('title') }}"
                            class="w-full rounded-md bg-[#0b0f17] border border-yellow-500 px-3 py-2 text-white placeholder-yellow-200 focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-yellow-200">Tienda</label>
                        <select name="store_id"
                            class="w-full rounded-md bg-[#0b0f17] border border-yellow-500 px-3 py-2 text-white appearance-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Todas</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->store_id }}" @selected(request('store_id') == $s->store_id)>{{ $s->store_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-yellow-200">Categoría</label>
                        <select name="category_id"
                            class="w-full rounded-md bg-[#0b0f17] border border-yellow-500 px-3 py-2 text-white appearance-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Todas</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->category_id }}" @selected(request('category_id') == $c->category_id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-yellow-200">Idioma</label>
                        <select name="language_id"
                            class="w-full rounded-md bg-[#0b0f17] border border-yellow-500 px-3 py-2 text-white appearance-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Todos</option>
                            @foreach($languages as $l)
                                <option value="{{ $l->language_id }}" @selected(request('language_id') == $l->language_id)>{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <label class="inline-flex items-center text-yellow-200">
                            <input type="checkbox" name="available_only" value="1" class="mr-2 bg-[#0b0f17] border-yellow-500" @checked(request()->boolean('available_only'))>
                            Solo disponibles
                        </label>
                    </div>

                    <div class="md:col-span-6 text-right">
                        <button class="blockbuster-btn">Buscar</button>
                    </div>
                </form>
            </div>

            {{-- INFO PILL --}}
            <div class="flex flex-wrap gap-3 text-xs text-white/70">
                <span class="pill">Búsqueda por título, categoría, actor, idioma</span>
                <span class="pill">Disponibilidad por sucursal</span>
                <span class="pill">Historial de rentas & pagos</span>
            </div>
        </div>
    </div>
</x-app-layout>
