<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Gestión de Rentas</h2>
    </x-slot>

    <div class="p-6 space-y-8">

        {{-- Mensajes de éxito / error --}}
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
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

        {{-- 🔍 Buscador y botón Nueva Renta --}}
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Rentas registradas en tu sucursal</h3>
            <button id="abrirModal" class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 transition">
                Nueva Renta
            </button>
        </div>

        <input type="text" id="buscadorRentas" placeholder="Buscar por cliente o película..."
            class="w-full border rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500">

        {{-- 📋 Tabla de rentas --}}
        <table class="table-auto w-full border border-gray-300 mt-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Película</th>
                    <th class="px-4 py-2">Fecha de Renta</th>
                    <th class="px-4 py-2">Devolución</th>
                    <th class="px-4 py-2">Cargo por retraso</th>
                    <th class="px-4 py-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaRentas">
                @foreach ($rentas as $r)
                    @php
                        $diasRetraso = null;
                        $cargo = null;
                        if (!$r->return_date) {
                            $diasRetraso = \Carbon\Carbon::parse($r->rental_date)
                                ->addDays($r->rental_duration)
                                ->diffInDays(now(), false);
                            $cargo = $diasRetraso > 0 ? $diasRetraso * 20 : 0; // 20 pesos por día
                        }
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $r->cliente }}</td>
                        <td class="px-4 py-2">{{ $r->pelicula }}</td>
                        <td class="px-4 py-2">{{ $r->rental_date }}</td>
                        <td class="px-4 py-2">
                            {{ $r->return_date ? $r->return_date : 'No devuelta' }}
                        </td>
                        <td class="px-4 py-2 text-red-600">
                            {{ $cargo ? '$' . $cargo : '-' }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if (!$r->return_date)
                                <!-- Botón para abrir el modal de devolución -->
                                <button type="button" onclick="abrirModal({{ $r->inventory_id }})"
                                    class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 transition">
                                    Registrar devolución
                                </button>
                            @else
                                <span class="text-gray-400">Completada</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 🪟 Modal Nueva Renta --}}
    <div id="modalRenta"
        class="hidden fixed inset-0 bg-gray-700 bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-[80%] h-[80%] border-4 border-black rounded-xl shadow-2xl p-10 overflow-y-auto relative">
            <h2 class="text-2xl font-bold mb-6">Registrar nueva renta</h2>

            <form method="POST" action="{{ route('empleado.rentas.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-medium text-gray-700">Cliente</label>
                    <select name="customer_id" class="w-full border rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($clientes as $c)
                            <option value="{{ $c->customer_id }}">
                                {{ $c->first_name }} {{ $c->last_name }} - {{ $c->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Película</label>
                    <select name="inventory_id" class="w-full border rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach ($inventario as $i)
                            <option value="{{ $i->inventory_id }}">
                                {{ $i->title }} ({{ $i->language }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="cerrarModal"
                        class="px-4 py-2 border border-gray-400 rounded-md hover:bg-gray-100">Cancelar</button>
                    <button type="submit"
                        class="px-6 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:ring-2 focus:ring-gray-400 transition">
                        Guardar renta
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🪟 Modal Devolución --}}
    <div id="modalDevolucion"
        class="fixed inset-0 hidden bg-gray-700 bg-opacity-60 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white p-6 rounded-xl border-4 border-black shadow-2xl w-[70%]">
            <h2 class="text-xl font-bold mb-4">Registrar devolución</h2>

            <form action="{{ route('empleado.historial.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inventory_id" id="inventory_id_modal">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Estado de la película</label>
                    <select name="estado" class="border w-full p-2 rounded-md">
                        <option value="Perfecto">Perfecto</option>
                        <option value="Dañado">Dañado</option>
                        <option value="Perdido">Perdido</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="border w-full p-2 rounded-md"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarModal()"
                        class="px-4 py-2 border rounded-md hover:bg-gray-100">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 border border-black rounded-md hover:bg-gray-100">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS de modales y buscador --}}
    <script>
        // Modal nueva renta
        const abrir = document.getElementById('abrirModal');
        const cerrar = document.getElementById('cerrarModal');
        const modal = document.getElementById('modalRenta');
        abrir.addEventListener('click', () => modal.classList.remove('hidden'));
        cerrar.addEventListener('click', () => modal.classList.add('hidden'));

        // Modal devolución
        function abrirModal(id) {
            document.getElementById('inventory_id_modal').value = id;
            const modalDev = document.getElementById('modalDevolucion');
            modalDev.classList.remove('hidden');
            modalDev.classList.add('flex');
        }

        function cerrarModal() {
            const modalDev = document.getElementById('modalDevolucion');
            modalDev.classList.add('hidden');
            modalDev.classList.remove('flex');
        }

        // Buscador
        document.getElementById('buscadorRentas').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('#tablaRentas tr').forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
