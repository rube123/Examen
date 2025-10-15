<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white p-4 rounded border mb-6 grid md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Tienda</label>
                    <select name="store_id" class="w-full border rounded px-3 py-2">
                        <option value="">Todas</option>
                        @foreach($tiendas as $t)
                            <option value="{{ $t->store_id }}" @selected(request('store_id') == $t->store_id)>{{ $t->store_id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Categoría (Top Películas)</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2">
                        <option value="">Todas</option>
                        @foreach($categorias as $c)
                            <option value="{{ $c->category_id }}" @selected(request('category_id') == $c->category_id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Filtrar</button>
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded border">Limpiar</a>
                </div>
            </form>

            {{-- Exportaciones rápidas (conservan filtros actuales) --}}
            <div class="bg-white p-4 rounded border mb-6 flex flex-wrap gap-3">
                <a class="px-3 py-2 rounded border"
                   href="{{ route('admin.reports.csv', array_merge(request()->query(), ['type'=>'rentas_tienda'])) }}">
                    CSV · Rentas por tienda
                </a>
                <a class="px-3 py-2 rounded border"
                   href="{{ route('admin.reports.csv', array_merge(request()->query(), ['type'=>'ingresos_tienda'])) }}">
                    CSV · Ingresos por tienda
                </a>
                <a class="px-3 py-2 rounded border"
                   href="{{ route('admin.reports.csv', array_merge(request()->query(), ['type'=>'peliculas_top'])) }}">
                    CSV · Películas top
                </a>
                <a class="px-3 py-2 rounded border"
                   href="{{ route('admin.reports.csv', array_merge(request()->query(), ['type'=>'clientes_top'])) }}">
                    CSV · Clientes top
                </a>
            </div>

            {{-- Rentas por tienda --}}
            <div class="bg-white p-4 rounded border mb-6">
                <h3 class="font-semibold mb-3">Rentas por tienda</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4">Store</th>
                                <th class="text-left py-2 pr-4">Tienda</th>
                                <th class="text-left py-2">Rentas</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rentas_por_tienda as $row)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $row->store_id }}</td>
                                <td class="py-2 pr-4">{{ $row->store_label }}</td>
                                <td class="py-2">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-3 text-gray-500">Sin datos.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $rentas_por_tienda->links() }}
            </div>

            {{-- Ingresos por tienda --}}
            <div class="bg-white p-4 rounded border mb-6">
                <h3 class="font-semibold mb-3">Ingresos por tienda</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4">Store</th>
                                <th class="text-left py-2 pr-4">Tienda</th>
                                <th class="text-left py-2">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($ingresos_por_tienda as $row)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $row->store_id }}</td>
                                <td class="py-2 pr-4">{{ $row->store_label }}</td>
                                <td class="py-2">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-3 text-gray-500">Sin datos.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $ingresos_por_tienda->links() }}
            </div>

            {{-- Películas más rentadas --}}
            <div class="bg-white p-4 rounded border mb-6">
                <h3 class="font-semibold mb-3">Películas más rentadas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4">ID</th>
                                <th class="text-left py-2 pr-4">Título</th>
                                <th class="text-left py-2">Veces</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($peliculas_top as $row)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $row->film_id }}</td>
                                <td class="py-2 pr-4">{{ $row->title }}</td>
                                <td class="py-2">{{ $row->veces }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-3 text-gray-500">Sin datos.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $peliculas_top->links() }}
            </div>

            {{-- Clientes top --}}
            <div class="bg-white p-4 rounded border">
                <h3 class="font-semibold mb-3">Clientes top</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 pr-4">ID</th>
                                <th class="text-left py-2 pr-4">Nombre</th>
                                <th class="text-left py-2 pr-4">Email</th>
                                <th class="text-left py-2">Veces</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($clientes_top as $row)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $row->customer_id }}</td>
                                <td class="py-2 pr-4">{{ $row->nombre }}</td>
                                <td class="py-2 pr-4">{{ $row->email }}</td>
                                <td class="py-2">{{ $row->veces }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-3 text-gray-500">Sin datos.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $clientes_top->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
