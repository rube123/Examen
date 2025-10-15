<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Gestión de Tiendas</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.tiendas.create') }}"
                class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md shadow hover:bg-blue-700 transition duration-200">
                Nueva Tienda
            </a>
        </div>


        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Gerente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última actualización</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($tiendas as $t)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $t->store_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $t->manager }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $t->address }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $t->last_update }}</td>
                            <td class="px-6 py-4 text-center text-sm">
                                {{-- Botón Editar (amarillo) --}}
                                <a href="{{ route('admin.tiendas.edit', $t->store_id) }}"
                                    class="inline-block bg-yellow-500 text-black font-semibold px-4 py-2 rounded-md shadow hover:bg-yellow-600 transition duration-200">
                                    Editar
                                </a>


                                {{-- Botón Eliminar (rojo) --}}
                                <form action="{{ route('admin.tiendas.destroy', $t->store_id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('¿Estás seguro de eliminar esta tienda?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-block bg-red-600 text-white font-semibold px-4 py-2 rounded-md shadow hover:bg-red-700 transition duration-200">
                                        Eliminar
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>