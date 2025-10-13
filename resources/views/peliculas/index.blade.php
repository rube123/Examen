<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Inventario de la Sucursal</h2>
    </x-slot>

    <div class="p-6 space-y-6">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        <form method="GET" class="flex space-x-2">
            <input type="text" name="search" placeholder="Buscar por título, categoría o idioma..."
                class="w-full p-3 border rounded-md" value="{{ request('search') }}">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Buscar</button>
        </form>

        <table class="w-full mt-4 border text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Título</th>
                    <th class="px-4 py-2">Categoría</th>
                    <th class="px-4 py-2">Idioma</th>
                    <th class="px-4 py-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peliculas as $p)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $p->title }}</td>
                        <td class="px-4 py-2">{{ $p->category ?? 'Sin categoría' }}</td>
                        <td class="px-4 py-2">{{ $p->language }}</td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <a href="{{ route('empleado.peliculas.historial', $p->inventory_id) }}"
                                class="px-3 py-1 border rounded-md hover:bg-gray-100">Historial</a>

                            <form action="{{ route('empleado.peliculas.marcar', $p->inventory_id) }}" method="POST" class="inline">
                                @csrf
                                <select name="status" class="border rounded p-1 text-sm">
                                    <option value="dañada">Dañada</option>
                                    <option value="perdida">Perdida</option>
                                </select>
                                <button type="submit"
                                    class="px-3 py-1 border border-black rounded-md hover:bg-gray-100">Marcar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $peliculas->links() }}</div>
    </div>
</x-app-layout>
