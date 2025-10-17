<!-- <x-app-layout>
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
                <div style="max-width: 600px; height: 400px;">
                    <canvas id="chartPeliculas"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">💰 Ingresos por tienda</h3>
                <div style="max-width: 600px; height: 400px;">
                    <canvas id="chartTiendas"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">👥 Clientes con más rentas</h3>
                <div style="max-width: 600px; height: 400px;">
                    <canvas id="chartClientes"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow flex flex-col items-center">
                <h3 class="font-semibold mb-3 text-center">📁 Rentas por categoría</h3>
                <div style="max-width: 600px; height: 400px;">
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

</x-app-layout> -->


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                📊 Reportes y Estadísticas
            </h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">Rango: {{ $fechaInicio }} → {{ $fechaFin }}</span>
        </div>
    </x-slot>

    <div class="p-6 space-y-6">

        {{-- Filtros / Export --}}
        <form method="GET"
              class="flex flex-wrap gap-3 bg-white dark:bg-gray-900/60 backdrop-blur rounded-2xl border border-gray-100 dark:border-gray-800 p-4 md:p-5 shadow-sm items-end">
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Desde</label>
                <input type="date" name="inicio" value="{{ $fechaInicio }}"
                       class="border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Hasta</label>
                <input type="date" name="fin" value="{{ $fechaFin }}"
                       class="border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex flex-col">
                <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Sucursal</label>
                <select name="store_id"
                        class="border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Todas</option>
                    @foreach($tiendas as $t)
                        <option value="{{ $t->store_id }}" {{ $storeId == $t->store_id ? 'selected' : '' }}>
                            Tienda {{ $t->store_id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1"></div>

            <button
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 active:scale-[.98] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 12h16M4 6h16M4 18h7"/></svg>
                Filtrar
            </button>

            <a href="{{ route('admin.reportes.csv', request()->all()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl hover:bg-emerald-700 active:scale-[.98] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M5 4h14a1 1 0 0 1 1 1v10h-2V6H6v12h5v2H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/><path d="M19 15h-4v-2h4V9l4 4-4 4v-2Z"/></svg>
                CSV
            </a>

            <a href="{{ route('admin.reportes.pdf', request()->all()) }}"
               class="inline-flex items-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-xl hover:bg-rose-700 active:scale-[.98] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/><path class="fill-white/90" d="M14 2v5h5"/></svg>
                PDF
            </a>
        </form>

        {{-- Resumen rápido --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/20 border border-blue-100 dark:border-blue-900">
                <div class="text-xs text-blue-700 dark:text-blue-300 mb-1">Ingresos totales</div>
                <div class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                    ${{ number_format($ingresosTotales ?? 0, 2) }}
                </div>
            </div>
            <div class="rounded-2xl p-4 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-100 dark:border-emerald-900">
                <div class="text-xs text-emerald-700 dark:text-emerald-300 mb-1">Rentas</div>
                <div class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">
                    {{ $totalRentas ?? 0 }}
                </div>
            </div>
            <div class="rounded-2xl p-4 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/30 dark:to-yellow-950/20 border border-amber-100 dark:border-amber-900">
                <div class="text-xs text-amber-700 dark:text-amber-300 mb-1">Clientes activos</div>
                <div class="text-2xl font-semibold text-amber-900 dark:text-amber-100">
                    {{ $clientesActivos ?? 0 }}
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $hasPeliculas = !empty($topPeliculas ?? []);
                $hasTiendas = !empty($ingresosPorTienda ?? []);
                $hasClientes = !empty($clientesTop ?? []);
                $hasCategorias = !empty($rentasPorCategoria ?? []);
            @endphp

            {{-- 🎬 Películas más rentadas --}}
            <div class="bg-white dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 shadow-sm">
                <h3 class="font-semibold mb-3 text-gray-800 dark:text-gray-100 text-center">🎬 Películas más rentadas</h3>
                @if($hasPeliculas)
                    <canvas id="chartPeliculas" class="w-full h-[340px]"></canvas>
                @else
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-10">Sin datos en el rango seleccionado.</div>
                @endif
            </div>

            {{-- 💰 Ingresos por tienda --}}
            <div class="bg-white dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 shadow-sm">
                <h3 class="font-semibold mb-3 text-gray-800 dark:text-gray-100 text-center">💰 Ingresos por tienda</h3>
                @if($hasTiendas)
                    <canvas id="chartTiendas" class="w-full h-[340px]"></canvas>
                @else
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-10">Sin datos en el rango seleccionado.</div>
                @endif
            </div>

            {{-- 👥 Clientes con más rentas --}}
            <div class="bg-white dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 shadow-sm">
                <h3 class="font-semibold mb-3 text-gray-800 dark:text-gray-100 text-center">👥 Clientes con más rentas</h3>
                @if($hasClientes)
                    <canvas id="chartClientes" class="w-full h-[340px]"></canvas>
                @else
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-10">Sin datos en el rango seleccionado.</div>
                @endif
            </div>

            {{-- 📁 Rentas por categoría --}}
            <div class="bg-white dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 shadow-sm">
                <h3 class="font-semibold mb-3 text-gray-800 dark:text-gray-100 text-center">📁 Rentas por categoría</h3>
                @if($hasCategorias)
                    <canvas id="chartCategorias" class="w-full h-[340px]"></canvas>
                @else
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 py-10">Sin datos en el rango seleccionado.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Librerías --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        // Datos desde el backend
        const peliculas = @json($topPeliculas ?? []);
        const tiendas = @json($ingresosPorTienda ?? []);
        const clientes = @json($clientesTop ?? []);
        const categorias = @json($rentasPorCategoria ?? []); // <-- asegúrate de enviarlo desde el controlador

        // Utilidades
        const fmtCurrency = (v) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 2 }).format(v ?? 0);
        const fmtNumber = (v) => new Intl.NumberFormat('es-MX').format(v ?? 0);

        // Paleta agradable (auto)
        const palette = [
            '#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#06B6D4','#84CC16','#F97316',
            '#EC4899','#14B8A6','#A855F7','#22C55E','#EAB308','#0EA5E9'
        ];

        // Opciones comunes elegantes
        const commonOptions = (title = '') => ({
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const label = ctx.dataset.label ? ctx.dataset.label + ': ' : '';
                            const raw = ctx.raw ?? 0;
                            // Si el dataset está marcado como currency, formatear MXN
                            return (ctx.dataset.currency ? label + fmtCurrency(raw) : label + fmtNumber(raw));
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    offset: 4,
                    clamp: true,
                    formatter: (v, ctx) => ctx.dataset.currency ? fmtCurrency(v) : fmtNumber(v),
                    font: { size: 10 }
                },
                title: {
                    display: !!title,
                    text: title,
                    font: { size: 14, weight: '600' },
                    padding: { top: 0, bottom: 8 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => fmtNumber(value) },
                    grid: { color: 'rgba(0,0,0,.06)' }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        autoSkip: true,
                        maxRotation: 0,
                        callback: function(value, index, ticks) {
                            const label = this.getLabelForValue(value);
                            return ('' + label).length > 14 ? ('' + label).slice(0, 14) + '…' : label;
                        }
                    }
                }
            }
        });

        // Render helper
        function mountChart(id, config) {
            const el = document.getElementById(id);
            if (!el) return null;
            return new Chart(el.getContext('2d'), config);
        }

        // 🎬 Películas más rentadas (Bar)
        if (peliculas.length) {
            mountChart('chartPeliculas', {
                type: 'bar',
                data: {
                    labels: peliculas.map(p => p.title),
                    datasets: [{
                        label: 'Total de rentas',
                        data: peliculas.map(p => Number(p.total) || 0),
                        backgroundColor: palette.map((c, i) => palette[i % palette.length]),
                        borderRadius: 8,
                    }]
                },
                options: commonOptions()
            });
        }

        // 💰 Ingresos por tienda (Doughnut con moneda)
        if (tiendas.length) {
            mountChart('chartTiendas', {
                type: 'doughnut',
                data: {
                    labels: tiendas.map(t => 'Tienda ' + t.store_id),
                    datasets: [{
                        label: 'Ingresos',
                        data: tiendas.map(t => Number(t.total_ingresos) || 0),
                        backgroundColor: tiendas.map((_, i) => palette[i % palette.length]),
                        currency: true
                    }]
                },
                options: {
                    ...commonOptions(),
                    plugins: {
                        ...commonOptions().plugins,
                        legend: { position: 'right' }
                    },
                    cutout: '60%'
                }
            });
        }

        // 👥 Clientes con más rentas (Horizontal Bar si hay muchos)
        if (clientes.length) {
            const labels = clientes.map(c => c.cliente);
            const data = clientes.map(c => Number(c.total_rentas) || 0);
            const horizontal = labels.length > 6;

            mountChart('chartClientes', {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Rentas',
                        data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderRadius: 8,
                    }]
                },
                options: {
                    ...commonOptions(),
                    indexAxis: horizontal ? 'y' : 'x',
                }
            });
        }

        // 📁 Rentas por categoría (Radar si son varias, si no bar)
        if (categorias.length) {
            const labels = categorias.map(c => c.categoria ?? c.name ?? c.label);
            const values = categorias.map(c => Number(c.total_rentas ?? c.total ?? 0));

            const type = labels.length >= 5 ? 'radar' : 'bar';

            mountChart('chartCategorias', {
                type,
                data: {
                    labels,
                    datasets: [{
                        label: 'Rentas',
                        data: values,
                        backgroundColor: type === 'radar'
                            ? 'rgba(59,130,246,0.2)'
                            : labels.map((_, i) => palette[i % palette.length]),
                        borderColor: type === 'radar' ? '#3B82F6' : undefined,
                        pointBackgroundColor: type === 'radar' ? '#3B82F6' : undefined,
                        borderWidth: type === 'radar' ? 2 : 0,
                        fill: true
                    }]
                },
                options: commonOptions()
            });
        }
    </script>
</x-app-layout>
