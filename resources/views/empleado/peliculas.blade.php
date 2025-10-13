<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Inventario de películas</h2>
    </x-slot>

    <div class="p-8 space-y-6">

        {{-- Barra de búsqueda --}}
        <form method="GET" action="{{ route('empleado.peliculas') }}" class="flex justify-end mb-6">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar por título, actor, categoría o idioma..."
                class="w-1/2 border border-gray-300 rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit"
                class="ml-3 px-6 py-3 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:ring-2 focus:ring-gray-400 transition">
                Buscar
            </button>
        </form>

        {{-- Tabla de películas --}}
        <div class="bg-white p-6 rounded-lg shadow-lg border border-black">
            <h3 class="text-lg font-bold mb-4 text-gray-800">Películas disponibles</h3>

            <table class="table-auto w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">Título</th>
                        <th class="px-4 py-2 border-b">Categorías</th>
                        <th class="px-4 py-2 border-b">Actores</th>
                        <th class="px-4 py-2 border-b">Idioma</th>
                        <th class="px-4 py-2 border-b">Año</th>
                        <th class="px-4 py-2">Stock</th>
                        <th class="px-4 py-2 border-b">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peliculas as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2">{{ $p->title }}</td>
                            <td class="border px-4 py-2">{{ $p->categories ?? 'Sin categoría' }}</td>
                            <td class="border px-4 py-2">{{ $p->actors ?? 'Sin actores' }}</td>
                            <td class="border px-4 py-2">{{ $p->language }}</td>
                            <td class="border px-4 py-2 text-center">{{ $p->release_year }}</td>
                            <td class="px-4 py-2 text-center">{{ $p->stock }}</td>
                            <td class="border px-4 py-2 text-center">
                                <div class="flex justify-center gap-3">
                                    {{-- Marcar dañada --}}
                                    <form method="POST" action="{{ route('empleado.peliculas.marcar', $p->film_id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="dañada">
                                        <button type="submit"
                                            class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-red-100 focus:ring-2 focus:ring-red-400 transition">
                                            Dañada
                                        </button>
                                    </form>

                                    {{-- Ver histórico --}}
                                    <a href="{{ route('empleado.peliculas.historial', $p->film_id) }}"
                                        class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:ring-2 focus:ring-gray-400 transition">
                                        Historial
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
