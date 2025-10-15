<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar Tienda</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow">
            <form method="POST" action="{{ route('admin.tiendas.update', $tienda->store_id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Dirección:</label>
                    <input type="text" name="address" value="{{ old('address', $tienda->address) }}"
                        class="border-gray-300 rounded-md shadow-sm w-full focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">ID del Gerente:</label>
                    <input type="number" name="manager_staff_id" value="{{ old('manager_staff_id', $tienda->manager_staff_id) }}"
                        class="border-gray-300 rounded-md shadow-sm w-full focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.tiendas') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-black rounded hover:bg-yellow-600">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
