<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Gestión de Cuentas</h2>
    </x-slot>

    <div class="p-6 bg-gray-100">
        @if (session('status'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">ID</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empleados as $e)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $e->staff_id }}</td>
                        <td class="p-3">{{ $e->first_name }} {{ $e->last_name }}</td>
                        <td class="p-3">{{ $e->email }}</td>
                        <td class="p-3">
                            @if ($e->active)
                                <span class="px-2 py-1 bg-green-200 text-green-800 rounded">Activo</span>
                            @else
                                <span class="px-2 py-1 bg-red-200 text-red-800 rounded">Bloqueado</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('admin.cuentas.reset', $e->staff_id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded">
                                    Resetear
                                </button>
                            </form>

                            <form action="{{ route('admin.cuentas.toggle', $e->staff_id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    {{ $e->active ? 'Bloquear' : 'Desbloquear' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
