<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Historial de rentas — {{ $cliente->first_name }} {{ $cliente->last_name }}</h2>
    </x-slot>

    <div class="bg-white p-8 shadow-md rounded-lg mx-auto max-w-4xl mt-10">
        <table class="table-auto w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border-b">Película</th>
                    <th class="px-4 py-2 border-b">Fecha de renta</th>
                    <th class="px-4 py-2 border-b">Fecha de devolución</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rentas as $r)
                    <tr>
                        <td class="border px-4 py-2">{{ $r->title }}</td>
                        <td class="border px-4 py-2">{{ $r->rental_date }}</td>
                        <td class="border px-4 py-2">
                            {{ $r->return_date ?? 'Aún no devuelta' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
