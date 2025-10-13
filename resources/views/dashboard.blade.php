@php
    $user = Auth::user();
@endphp

@if($user && $user->isRole('empleado'))
    <script>
        window.location.href = "{{ route('empleado.dashboard') }}";
    </script>
@endif

<x-app-layout>
<<<<<<< HEAD
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

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

      // Fallbacks por si no vienes desde un controlador que pase estos datos:
      if (!isset($stores)) {
          $stores = \DB::table('store')->select('store_id')->orderBy('store_id')->get();
      }
      if (!isset($languages)) {
          $languages = \DB::table('language')->select('language_id','name')->orderBy('name')->get();
      }
      if (!isset($categories)) {
          $categories = \DB::table('category')->select('category_id','name')->orderBy('name')->get();
      }
=======
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if ($errors->has('auth'))
    <div class="p-3 mb-4 bg-red-100 text-red-700 rounded">
        {{ $errors->first('auth') }}
    </div>
@endif


    @php
        $user = Auth::user();
        $role = optional($user->role)->name; // obtén el nombre del rol desde la relación

        // Si el nombre del rol en la BD está en español, ajusta aquí
        $labels = [
            'admin' => 'Administrador',
            'empleado' => 'Empleado',
            'cliente' => 'Cliente',
            'publico' => 'Público general',
        ];

        $label = $labels[$role] ?? ucfirst($role ?? 'Sin rol');
    @endphp



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">
                        Bienvenido, {{ $label }}
                    </h1>
>>>>>>> 6240fd4c090c320552a32d9a68f99d8f9dc67fd5

      // Evitar error si no existe la tabla notifications
      $hasNotificationsTable = \Schema::hasTable('notifications');
  @endphp

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="text-2xl font-bold mb-1">Bienvenido, {{ $label }}</h1>
          <p class="mb-6">Estás autenticado como <strong>{{ $user->name }}</strong> ({{ $user->email }}).</p>

          {{-- ALERTAS (solo si existe la tabla y hay notificaciones) --}}
          @if($role === 'customer' && $hasNotificationsTable && method_exists($user, 'unreadNotifications') && $user->unreadNotifications->count())
            <div class="mb-6 rounded border border-amber-300 bg-amber-50 p-4">
              <h3 class="font-semibold mb-2 text-amber-800">Alertas</h3>
              <ul class="list-disc pl-5 text-sm text-amber-900">
                @foreach($user->unreadNotifications as $n)
                  <li class="mb-1">
                    {{ $n->data['message'] ?? 'Tienes una notificación de tu renta.' }}
                    <span class="text-xs text-amber-700">· {{ $n->created_at->diffForHumans() }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Accesos rápidos por rol --}}
          @switch($role)
            @case('customer')
              <h3 class="text-lg font-semibold mb-3">Accesos rápidos</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <a href="{{ route('customer.catalog') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-blue-600 text-blue-700 bg-white hover:bg-blue-50
                          transition focus:outline-none focus:ring-2 focus:ring-blue-600/40">
                  Catálogo
                </a>
                <a href="{{ route('customer.rentals') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50
                          transition focus:outline-none focus:ring-2 focus:ring-slate-600/30">
                  Mis rentas
                </a>
                <a href="{{ route('customer.payments') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50
                          transition focus:outline-none focus:ring-2 focus:ring-slate-600/30">
                  Pagos
                </a>
                <a href="{{ route('customer.charges') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-amber-600 text-amber-700 bg-white hover:bg-amber-50
                          transition focus:outline-none focus:ring-2 focus:ring-amber-600/30">
                  Cargos
                </a>
              </div>

              {{-- BUSCADOR RÁPIDO: manda a /customer/catalog con filtros --}}
              <form method="GET" action="{{ route('customer.catalog') }}" class="grid md:grid-cols-6 gap-3 bg-gray-50 p-4 rounded border">
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium mb-1">Título</label>
                  <input type="text" name="title" value="{{ request('title') }}" class="w-full border rounded px-3 py-2" placeholder="Buscar por título...">
                </div>

                <div>
                  <label class="block text-sm font-medium mb-1">Tienda</label>
                  <select name="store_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todas</option>
                    @foreach($stores as $s)
                      <option value="{{ $s->store_id }}" @selected(request('store_id') == $s->store_id)>{{ $s->store_id }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-1">Categoría</label>
                  <select name="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todas</option>
                    @foreach($categories as $c)
                      <option value="{{ $c->category_id }}" @selected(request('category_id') == $c->category_id)>{{ $c->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-1">Idioma</label>
                  <select name="language_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($languages as $l)
                      <option value="{{ $l->language_id }}" @selected(request('language_id') == $l->language_id)>{{ $l->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="flex items-end">
                  <label class="inline-flex items-center">
                    <input type="checkbox" name="available_only" value="1" class="mr-2" @checked(request()->boolean('available_only'))>
                    Solo disponibles ahora
                  </label>
                </div>

                <div class="md:col-span-6 text-right">
                  <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg
                                 bg-blue-600 text-white hover:bg-blue-700
                                 focus:outline-none focus:ring-2 focus:ring-blue-600/40 shadow-sm">
                    Buscar
                  </button>
                </div>
              </form>
            @break

            @case('employee')
              <h3 class="text-lg font-semibold mb-3">Accesos rápidos</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ url('/employee/customers') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-blue-600 text-blue-700 bg-white hover:bg-blue-50">Clientes</a>
                <a href="{{ url('/employee/inventory') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50">Inventario</a>
                <a href="{{ url('/employee/rentals') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50">Rentas</a>
                <a href="{{ url('/employee/returns') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-amber-600 text-amber-700 bg-white hover:bg-amber-50">Devoluciones</a>
              </div>
            @break

            @case('admin')
              <h3 class="text-lg font-semibold mb-3">Accesos rápidos</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ url('/admin/stores') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-blue-600 text-blue-700 bg-white hover:bg-blue-50">Tiendas</a>
                <a href="{{ url('/admin/employees') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50">Empleados</a>
                <a href="{{ url('/admin/catalog') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-slate-700 text-slate-800 bg-white hover:bg-slate-50">Catálogo</a>
                <a href="{{ url('/admin/reports') }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-lg
                          border border-amber-600 text-amber-700 bg-white hover:bg-amber-50">Reportes</a>
              </div>
            @break

            @default
              <p class="text-gray-600">Tu usuario no tiene un rol asignado. Contacta al administrador.</p>
          @endswitch
        </div>
      </div>
    </div>
<<<<<<< HEAD
  </div>
</x-app-layout>
=======
</x-app-layout>
>>>>>>> 6240fd4c090c320552a32d9a68f99d8f9dc67fd5
