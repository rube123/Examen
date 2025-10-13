<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Pagos
    </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">
    {{-- Resumen --}}
    <div class="mb-4 flex flex-wrap gap-4">
      <div class="px-4 py-2 rounded border bg-white">
        <span class="text-sm text-gray-600">Total en esta página:</span>
        <span class="ml-2 font-semibold">${{ number_format((float)($totalPage ?? 0), 2) }}</span>
      </div>
      <div class="px-4 py-2 rounded border bg-white">
        <span class="text-sm text-gray-600">Total histórico:</span>
        <span class="ml-2 font-semibold">${{ number_format((float)($totalAll ?? 0), 2) }}</span>
      </div>
    </div>

    @if($paginator->total() === 0)
      <div class="rounded border bg-white p-6">
        <p class="text-gray-700">No tienes pagos registrados.</p>
      </div>
    @else
      <div class="overflow-x-auto rounded border bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-700">
            <tr>
              <th class="text-left p-3 border-b"># Pago</th>
              <th class="text-left p-3 border-b">Fecha</th>
              <th class="text-left p-3 border-b">Monto</th>
              <th class="text-left p-3 border-b">Película</th>
              <th class="text-left p-3 border-b"># Renta</th>
              <th class="text-left p-3 border-b">Staff</th>
              <th class="text-left p-3 border-b">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($paginator as $p)
              <tr>
                <td class="p-3 align-top">{{ $p->payment_id }}</td>
                <td class="p-3 align-top">{{ \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d H:i') }}</td>
                <td class="p-3 align-top font-semibold">${{ number_format((float)$p->amount, 2) }}</td>
                <td class="p-3 align-top">
                  <a class="text-blue-700 hover:underline" href="{{ route('customer.films.show', $p->film_id) }}">
                    {{ $p->title }}
                  </a>
                </td>
                <td class="p-3 align-top">#{{ $p->rental_id }}</td>
                <td class="p-3 align-top">
                  @if(!empty($p->staff_name))
                    {{ $p->staff_name }} <span class="text-gray-500">(#{{ $p->staff_id }})</span>
                  @else
                    #{{ $p->staff_id ?? '—' }}
                  @endif
                </td>
                <td class="p-3 align-top">
                  <a class="text-sm text-blue-700 hover:underline" href="{{ route('customer.films.show', $p->film_id) }}">Ver película</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $paginator->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
