<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar cliente</h2>
    </x-slot>

    <div class="bg-white p-8 shadow-md rounded-lg mx-auto max-w-lg mt-10">
        <form method="POST" action="{{ route('empleado.clientes.update', $cliente->customer_id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <label for="first_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="first_name" id="first_name" value="{{ $cliente->first_name }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm p-3">
            </div>

            <div class="space-y-3">
                <label for="last_name" class="block text-sm font-medium text-gray-700">Apellido</label>
                <input type="text" name="last_name" id="last_name" value="{{ $cliente->last_name }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm p-3">
            </div>

            <div class="space-y-3">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ $cliente->email }}" class="w-full rounded-md border-gray-300 shadow-sm p-3">
            </div>

            <div class="space-y-3">
                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" name="phone" id="phone" value="{{ $cliente->phone }}" class="w-full rounded-md border-gray-300 shadow-sm p-3">
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="px-6 py-3 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                    Actualizar cliente
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
