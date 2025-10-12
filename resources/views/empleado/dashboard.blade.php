<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Panel del Empleado</h2>
    </x-slot>

    <div class="p-6 space-y-6">

        {{-- Mensajes de éxito o error --}}
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded">
                <strong>Errores:</strong>
                <ul class="list-disc ml-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

{{-- Formulario para registrar cliente --}}
<div class="bg-white p-6 shadow-sm rounded-lg">
    <h3 class="text-lg font-bold mb-8 text-gray-800">Registrar nuevo cliente</h3>

    <form method="POST" action="{{ route('empleado.clientes.store') }}" class="space-y-8">
        @csrf

        <div class="space-y-3">
            <label for="first_name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" id="first_name" name="first_name" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
        </div>

        <div class="space-y-3">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Apellido</label>
            <input type="text" id="last_name" name="last_name" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
        </div>

        <div class="space-y-3">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email"
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
        </div>

        <div class="space-y-3">
            <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono (opcional)</label>
            <input type="text" id="phone" name="phone"
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
        </div>

        <div class="pt-6">
            <button type="submit"
                class="px-6 py-3 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                Guardar cliente
            </button>
        </div>
    </form>
</div>


        {{-- Tabla de clientes existentes --}}
<div class="bg-white p-6 shadow-sm rounded-lg">
    <h3 class="text-lg font-bold mb-6 text-gray-800">Clientes registrados en tu sucursal</h3>

    <table class="table-auto w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-3 border-b font-medium text-gray-700">Nombre</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700">Email</th>
                <th class="px-4 py-3 border-b font-medium text-gray-700 text-center w-48">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="border px-4 py-3 text-gray-800">{{ $c->first_name }} {{ $c->last_name }}</td>
                    <td class="border px-4 py-3 text-gray-800">{{ $c->email }}</td>
                    <td class="border px-4 py-3 text-center">
    <div class="flex justify-center gap-3">

        {{-- Botón Actualizar --}}
        {{-- {{ route('empleado.clientes.edit', $c->id) }} --}}
        <a href=""
            class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
            Actualizar
        </a>

        {{-- Botón Eliminar --}}
        {{-- {{ route('empleado.clientes.destroy', $c->id) }}" method="POST"
            onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?') --}}
        <form action="">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                Eliminar
            </button>
        </form>

    </div>
</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
>

    </div>
</x-app-layout>