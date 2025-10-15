<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Panel de Administración</h2>
  </x-slot>

  <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <a href="{{ route('admin.dashboard') }}" class="bg-white border rounded-lg p-4 shadow hover:bg-gray-100">
      <div class="text-gray-500 text-sm">Tiendas</div>
      <div class="text-3xl font-bold">{{ $totalTiendas }}</div>
    </a>

    <a href="{{ route('admin.empleados') }}" class="bg-white border rounded-lg p-4 shadow hover:bg-gray-100">
      <div class="text-gray-500 text-sm">Empleados</div>
      <div class="text-3xl font-bold">{{ $totalEmpleados }}</div>
    </a>

    <a href="{{ route('admin.peliculas') }}" class="bg-white border rounded-lg p-4 shadow hover:bg-gray-100">
      <div class="text-gray-500 text-sm">Catálogo</div>
      <div class="text-3xl font-bold">{{ $totalClientes }}</div>
    </a>

    <a href="{{ route('admin.reportes') }}" class="bg-white border rounded-lg p-4 shadow hover:bg-gray-100">
      <div class="text-gray-500 text-sm">Reportes</div>
      <div class="text-3xl font-bold">{{ $totalRentas }}</div>
    </a>
  </div>
</x-app-layout>
