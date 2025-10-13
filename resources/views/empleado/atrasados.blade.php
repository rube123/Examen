<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rentas atrasadas</h2>
    </x-slot>

    <div class="p-8 bg-white shadow-md rounded-lg mt-10 max-w-6xl mx-auto">
        <h3 class="text-lg font-bold mb-6 text-gray-800">Listado de DVDs rentados fuera de plazo</h3>

        @if ($atrasados->isEmpty())
            <p class="text-gray-600">No hay rentas atrasadas en este momento 🎉</p>
        @else
            <table class="table-auto w-full border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b text-left">Cliente</th>
                        <th class="px-4 py-2 border-b text-left">Teléfono</th>
                        <th class="px-4 py-2 border-b text-left">Película</th>
                        <th class="px-4 py-2 border-b text-left">Fecha de renta</th>
                        <th class="px-4 py-2 border-b text-left">Días permitidos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($atrasados as $r)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2">{{ $r->cliente }}</td>
                            <td class="border px-4 py-2">{{ $r->phone }}</td>
                            <td class="border px-4 py-2">{{ $r->title }}</td>
                            <td class="border px-4 py-2">{{ $r->rental_date }}</td>
                            <td class="border px-4 py-2 text-center">{{ $r->rental_duration }} días</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
