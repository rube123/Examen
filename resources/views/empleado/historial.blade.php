<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Historial de movimientos</h2>
    </x-slot>

    <div class="p-6">
        <table class="table-auto w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b">Cliente</th>
                    <th class="px-4 py-2 border-b">Fecha de renta</th>
                    <th class="px-4 py-2 border-b">Fecha de devolución</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historial as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $h->first_name }} {{ $h->last_name }}</td>
                        <td class="border px-4 py-2">{{ $h->rental_date }}</td>
                        <td class="border px-4 py-2">{{ $h->return_date ?? 'No devuelta' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
