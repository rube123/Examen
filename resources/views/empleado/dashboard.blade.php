<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Panel del Empleado</h2>
    </x-slot>

    <div class="p-6 space-y-6" x-data="clienteApp()">

        <!-- Mensajes -->
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Clientes registrados en tu sucursal</h3>
            <button @click="abrirModal(null)"
                class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                Añadir Cliente
            </button>
        </div>

        <!-- Buscador -->
        <div class="mb-4">
            <input type="text" placeholder="Buscar cliente por nombre o email..."
                class="w-full border rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500" x-model="busqueda">
        </div>

        <!-- Tabla -->
        <div class="bg-white shadow-sm rounded-lg">
            <table class="table-auto w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $c)
                        <tr
                            x-show="filtraCliente('{{ strtolower($c->first_name . ' ' . $c->last_name . ' ' . $c->email) }}')">
                            <td class="border px-4 py-2">{{ $c->first_name }} {{ $c->last_name }}</td>
                            <td class="border px-4 py-2">{{ $c->email }}</td>
                            <td class="border px-4 py-2">
                                <div class="flex space-x-2">
                                    <button @click="abrirModal({ 
                                            id: {{ $c->customer_id }},
                                            first_name: '{{ addslashes($c->first_name) }}',
                                            last_name: '{{ addslashes($c->last_name) }}',
                                            email: '{{ addslashes($c->email) }}'
                                        })"
                                        class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100">
                                        Actualizar
                                    </button>

                                    <form action="{{ route('empleado.clientes.destroy', $c->customer_id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 border border-black text-black rounded-md shadow-md hover:bg-gray-100">
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

        <!-- Modal grande centrado -->
        <div x-show="mostrarModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-700/40 backdrop-blur-md z-50">

            <!-- Contenedor del modal -->
            <div class="bg-white w-[90vw] h-[90vh] rounded-2xl border-[8px] border-black shadow-[0_0_40px_rgba(0,0,0,0.8)] p-10 relative overflow-auto"
                @click.away="cerrarModal()">

                <!-- Título -->
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 border-b-4 border-black pb-4"
                    x-text="cliente.id ? 'Actualizar Cliente' : 'Añadir Cliente'"></h2>

                <!-- Formulario -->
                <form method="POST" :action="cliente.id 
                  ? '/empleado/clientes/' + cliente.id 
                  : '{{ route('empleado.clientes.store') }}'" class="grid grid-cols-2 gap-8 px-8">

                    @csrf
                    <template x-if="cliente.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                        <input type="text" name="first_name" x-model="cliente.first_name"
                            class="w-full border-2 border-gray-400 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Apellido</label>
                        <input type="text" name="last_name" x-model="cliente.last_name"
                            class="w-full border-2 border-gray-400 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" x-model="cliente.email"
                            class="w-full border-2 border-gray-400 rounded-lg p-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Botones -->
                    <div class="col-span-2 flex justify-end space-x-4 pt-6">
                        <button type="button" @click="cerrarModal()"
                            class="px-6 py-3 border-2 border-gray-600 rounded-lg hover:bg-gray-100 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 border-2 border-black rounded-lg shadow-lg text-black hover:bg-gray-100 focus:ring-2 focus:ring-gray-400 transition">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script Alpine -->
    <script>
        function clienteApp() {
            return {
                busqueda: '',
                mostrarModal: false,
                cliente: {},

                abrirModal(datos) {
                    this.cliente = datos ? { ...datos } : { first_name: '', last_name: '', email: '' };
                    this.mostrarModal = true;
                },
                cerrarModal() {
                    this.mostrarModal = false;
                },
                filtraCliente(texto) {
                    return texto.includes(this.busqueda.toLowerCase());
                }
            };
        }
    </script>
</x-app-layout>