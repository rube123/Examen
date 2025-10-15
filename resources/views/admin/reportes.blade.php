<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">📊 Reportes y Estadísticas</h2>
    </x-slot>

    <div class="p-6 space-y-6">

        {{-- Filtros --}}
        <form method="GET" class="flex flex-wrap gap-3 bg-white p-4 rounded shadow items-end">
            <div>
                <label class="text-sm text-gray-700">Desde:</label>
                <input type="date" name="inicio" value="{{ $fechaInicio }}" class="border rounded p-1">
            </div>
            <div>
                <label class="text-sm text-gray-700">Hasta:</label>
                <input type="date" name="fin" value="{{ $fechaFin }}" class="border rounded p-1">
            </div>
            <div>
                <label class="text-sm text-gray-700">Sucursal:</label>
                <select name="store_id" class="border rounded p-1">
                    <option value="">Todas</option>
                    @foreach($tiendas as $t)
                        <option value="{{ $t->store_id }}" {{ $storeId == $t->store_id ? 'selected' : '' }}>
                            Tienda {{ $t->store_id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filtrar</button>

            {{-- Exportar --}}
            <a href="{{ route('admin.reportes.csv') }}"
                class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Exportar CSV</a>
            <a href="{{ route('admin.reportes.pdf') }}"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Exportar PDF</a>
        </form>

        {{-- Gráficos --}}
        {{-- Gráficos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">🎬 Películas más rentadas</h3>
                <div style="max-width: 400px; height: 300px;">
                    <canvas id="chartPeliculas"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">💰 Ingresos por tienda</h3>
                <div style="max-width: 400px; height: 300px;">
                    <canvas id="chartTiendas"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">👥 Clientes con más rentas</h3>
                <div style="max-width: 400px; height: 300px;">
                    <canvas id="chartClientes"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">📁 Rentas por categoría</h3>
                <div style="max-width: 400px; height: 300px;">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const peliculas = @json($topPeliculas);
        const tiendas = @json($ingresosPorTienda);
        const clientes = @json($clientesTop);

        const commonOptions = {
            responsive: false,          // 👈 no ocupa toda la pantalla
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'bottom' },
            },
            scales: {
                y: { beginAtZero: true }
            }
        };

        // 🎬 Películas más rentadas
        new Chart(document.getElementById('chartPeliculas'), {
            type: 'bar',
            data: {
                labels: peliculas.map(p => p.title),
                datasets: [{
                    label: 'Total rentas',
                    data: peliculas.map(p => p.total),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                }]
            },
            options: commonOptions
        });

        // 💰 Ingresos por tienda
        new Chart(document.getElementById('chartTiendas'), {
            type: 'pie',
            data: {
                labels: tiendas.map(t => 'Tienda ' + t.store_id),
                datasets: [{
                    data: tiendas.map(t => t.total_ingresos),
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                }]
            },
            options: { responsive: false, plugins: { legend: { position: 'right' } } }
        });

        // 👥 Clientes con más rentas
        new Chart(document.getElementById('chartClientes'), {
            type: 'bar',
            data: {
                labels: clientes.map(c => c.cliente),
                datasets: [{
                    label: 'Rentas',
                    data: clientes.map(c => c.total_rentas),
                    backgroundColor: 'rgba(255,159,64,0.6)',
                }]
            },
            options: commonOptions
        });
    </script>

</x-app-layout>