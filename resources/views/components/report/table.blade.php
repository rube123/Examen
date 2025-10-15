{{-- resources/views/components/report/table.blade.php --}}
@props([
    'rows',                           // Paginador o colección (ideal: LengthAwarePaginator).
    'columns' => [],                  // ['clave_en_row' => 'Encabezado'].
    'empty' => 'Sin datos.',          // Texto cuando no hay registros.
])

<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b">
                @foreach($columns as $key => $header)
                    <th class="text-left py-2 pr-4">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr class="border-b">
                @foreach($columns as $key => $header)
                    <td class="py-2 pr-4">
                        {{ data_get($row, $key) }}
                    </td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ count($columns) }}" class="py-3 text-gray-500">{{ $empty }}</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Si es un paginador, muestra los links (Tailwind por defecto). --}}
@if(method_exists($rows, 'links'))
    <div class="mt-3">
        {{ $rows->links() }}
    </div>
@endif
