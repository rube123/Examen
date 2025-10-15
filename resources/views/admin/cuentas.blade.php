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
                            <!-- Switch para activar/desactivar -->
                            <form action="{{ route('admin.cuentas.toggle', $e->staff_id) }}" method="POST"
                                id="toggleForm{{ $e->staff_id }}">
                                @csrf
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                        onchange="document.getElementById('toggleForm{{ $e->staff_id }}').submit()"
                                        class="sr-only peer" {{ $e->active ? 'checked' : '' }}>
                                    <div
                                        class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                    </div>
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ $e->active ? 'Activo' : 'Bloqueado' }}
                                    </span>
                                </label>
                            </form>
                        </td>
                        <td class="p-3 text-center">
                            <!-- Botón de reseteo con SweetAlert -->
                            <button onclick="confirmReset({{ $e->staff_id }}, '{{ $e->first_name }} {{ $e->last_name }}')"
                                class="bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded">
                                Resetear
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <script>
                function confirmReset(id, nombre) {
                    Swal.fire({
                        title: '¿Resetear contraseña?',
                        text: `Esto asignará "empleado123" a la cuenta de ${nombre}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, resetear',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/cuentas/${id}/reset`;
                            const token = document.createElement('input');
                            token.type = 'hidden';
                            token.name = '_token';
                            token.value = '{{ csrf_token() }}';
                            form.appendChild(token);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            </script>

        </table>
    </div>
</x-app-layout>