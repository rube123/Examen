<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Gestión de Empleados</h2>
    </x-slot>

    <div class="p-6">
        <div class="flex justify-between mb-4">
            <a href="{{ route('admin.empleados.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Nuevo Empleado
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        <table class="min-w-full bg-white border border-gray-300 rounded shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">ID</th>
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Tienda</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $e)
                    <tr class="border-t">
                        <td class="p-2">{{ $e->staff_id }}</td>
                        <td class="p-2">{{ $e->first_name }} {{ $e->last_name }}</td>
                        <td class="p-2">{{ $e->email }}</td>
                        <td class="p-2">{{ $e->store_id }}</td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('admin.empleados.edit', $e->staff_id) }}"
                                class="text-blue-500 hover:underline">Editar</a>
                            <form action="{{ route('admin.empleados.destroy', $e->staff_id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que deseas eliminar a este empleado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-red px-3 py-1 rounded hover:bg-red-600">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>