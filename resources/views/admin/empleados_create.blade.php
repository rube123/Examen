<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Empleado</h2>
    </x-slot>

    <div class="p-6 max-w-lg mx-auto bg-white rounded shadow">
        <form method="POST" action="{{ route('admin.empleados.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-gray-700">Nombre</label>
                <input type="text" name="first_name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700">Apellido</label>
                <input type="text" name="last_name" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700">Contraseña</label>
                <input type="password" name="password" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700">Tienda</label>
                <select name="store_id" class="w-full border p-2 rounded" required>
                    @foreach($tiendas as $t)
                        <option value="{{ $t->store_id }}">{{ $t->store_id }}</option>
                    @endforeach
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
        </form>
    </div>
</x-app-layout>
