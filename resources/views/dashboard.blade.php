<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $role = optional($user->role)->name;
        $labels = [
            'admin' => 'Administrador',
            'employee' => 'Empleado de sucursal',
            'customer' => 'Cliente',
            'public' => 'Público general',
        ];
        $label = $labels[$role] ?? 'Sin rol';
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">
                        Bienvenido, {{ $label }}
                    </h1>

                    <p class="mb-4">
                        Estás autenticado como <strong>{{ $user->name }}</strong> ({{ $user->email }}).
                    </p>

                    {{-- Aquí luego puedes renderizar tarjetas/links según rol --}}
                    @if($role === 'admin')
                        <p>Accesos rápidos: Gestión de tiendas, empleados, catálogo, reportes…</p>
                    @elseif($role === 'employee')
                        <p>Accesos rápidos: Gestión de clientes, inventario y rentas…</p>
                    @elseif($role === 'customer')
                        <p>Accesos rápidos: Catálogo y mis rentas…</p>
                    @else
                        <p>Acceso limitado a consultas públicas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
