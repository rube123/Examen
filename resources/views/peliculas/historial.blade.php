<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Historial de movimientos</h2>
    </x-slot>

    <div class="p-6">
        <table class="w-full border text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Fecha de renta</th>
                    <th class="px-4 py-2">Fecha de devolución</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historial as $h)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $h->first_name }} {{ $h->last_name }}</td>
                        <td class="px-4 py-2">{{ $h->rental_date }}</td>
                        <td class="px-4 py-2">{{ $h->return_date ?? 'No devuelta' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Sin movimientos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
