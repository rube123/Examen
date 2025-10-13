<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Cargos pendientes
    </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">
    {{-- Resumen --}}
    <div class="mb-4 flex flex-wrap gap-4">
      <div class="px-4 py-2 rounded border bg-white">
        <span class="text-sm text-gray-600">Tarifa diaria atraso:</span>
        <span class="ml-2 font-semibold">${{ number_format((float)($dailyFee ?? 0), 2) }}</span>
      </div>
      <div class="px-4 py-2 rounded border bg-white">
        <span class="text-sm text-gray-600">Total a pagar:</span>
        <span class="ml-2 font-semibold">${{ number_format((float)($totalDue ?? 0), 2) }}</span>
      </div>
    </div>

    @if(($items ?? collect())->isEmpty())
      <div class="rounded border bg-white p-6">
        <p class="text-gray-700">No tienes cargos pendientes por atraso 🎉.</p>
      </div>
    @else
      <div class="overflow-x-auto rounded border bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-700">
            <tr>
              <th class="text-left p-3 border-b"># Renta</th>
              <th class="text-left p-3 border-b">Película</th>
              <th class="text-left p-3 border-b">Tienda</th>
              <th class="text-left p-3 border-b">Renta</th>
              <th class="text-left p-3 border-b">Vence</th>
              <th class="text-left p-3 border-b">Atraso (días)</th>
              <th class="text-left p-3 border-b">Cargo</th>
              <th class="text-left p-3 border-b">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($items as $it)
              <tr>
                <td class="p-3 align-top">{{ $it->rental_id }}</td>
                <td class="p-3 align-top">
                  <a class="text-blue-700 hover:underline" href="{{ route('customer.films.show', $it->film_id) }}">
                    {{ $it->title }}
                  </a>
                </td>
                <td class="p-3 align-top">#{{ $it->store_id }}</td>
                <td class="p-3 align-top">{{ $it->rental_at->format('Y-m-d H:i') }}</td>
                <td class="p-3 align-top">{{ $it->due_at->format('Y-m-d H:i') }}</td>
                <td class="p-3 align-top {{ $it->late_days > 0 ? 'text-red-600 font-semibold' : '' }}">
                  {{ $it->late_days }}
                </td>
                <td class="p-3 align-top font-semibold">
                  ${{ number_format((float)$it->fee, 2) }}
                </td>
                <td class="p-3 align-top">
                  {{-- Aquí podrías poner un botón para pagar (placeholder) --}}
                  <span class="text-gray-500 text-xs">Pagar en caja</span>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" class="p-3 text-right font-semibold">Total</td>
              <td class="p-3 font-semibold">${{ number_format((float)$totalDue, 2) }}</td>
              <td class="p-3"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    @endif
  </div>
</x-app-layout>
