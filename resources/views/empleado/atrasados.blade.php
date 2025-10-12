<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Rentas Atrasadas</h2>
    </x-slot>

    <div class="p-6">
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Teléfono</th>
                    <th class="px-4 py-2">Película</th>
                </tr>
            </thead>
            <tbody>
                @foreach($atrasados as $item)
                    <tr>
                        <td class="border px-4 py-2">{{ $item->customer }}</td>
                        <td class="border px-4 py-2">{{ $item->phone }}</td>
                        <td class="border px-4 py-2">{{ $item->title }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
