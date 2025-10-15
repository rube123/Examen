<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Panel de Administración</h2>
    </x-slot>

    <div class="p-6">
        {{-- Contenedor principal responsivo --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Tarjeta Tiendas --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-white border rounded-2xl p-6 shadow-md hover:shadow-lg hover:scale-[1.02] transition-transform duration-300 ease-in-out">
                <div class="text-gray-500 text-sm">🏬 Tiendas</div>
                <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ $totalTiendas }}</div>
            </a>

            {{-- Tarjeta Empleados --}}
            <a href="{{ route('admin.empleados') }}" 
               class="bg-white border rounded-2xl p-6 shadow-md hover:shadow-lg hover:scale-[1.02] transition-transform duration-300 ease-in-out">
                <div class="text-gray-500 text-sm">👨‍💼 Empleados</div>
                <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ $totalEmpleados }}</div>
            </a>

            {{-- Tarjeta Catálogo --}}
            <a href="{{ route('admin.peliculas') }}" 
               class="bg-white border rounded-2xl p-6 shadow-md hover:shadow-lg hover:scale-[1.02] transition-transform duration-300 ease-in-out">
                <div class="text-gray-500 text-sm">🎬 Catálogo</div>
                <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ $totalClientes }}</div>
            </a>

            {{-- Tarjeta Reportes --}}
            <a href="{{ route('admin.reportes') }}" 
               class="bg-white border rounded-2xl p-6 shadow-md hover:shadow-lg hover:scale-[1.02] transition-transform duration-300 ease-in-out">
                <div class="text-gray-500 text-sm">📊 Reportes</div>
                <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ $totalRentas }}</div>
            </a>

        </div>
    </div>
</x-app-layout>
