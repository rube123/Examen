<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Mis rentas
    </h2>
  </x-slot>

  @php
    // Configurable: cargo por día de atraso
    $dailyLateFee = (float) env('DAILY_LATE_FEE', 1.50);
  @endphp

  <div class="max-w-7xl mx-auto p-6">
    @if($paginator->total() === 0)
      <div class="rounded border bg-white p-6">
        <p class="text-gray-700">Todavía no tienes rentas registradas.</p>
      </div>
    @else
      <div class="overflow-x-auto rounded border bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-700">
            <tr>
              <th class="text-left p-3 border-b">#</th>
              <th class="text-left p-3 border-b">Película</th>
              <th class="text-left p-3 border-b">Renta</th>
              <th class="text-left p-3 border-b">Vence</th>
              <th class="text-left p-3 border-b">Devuelta</th>
              <th class="text-left p-3 border-b">Tienda</th>
              <th class="text-left p-3 border-b">Duración</th>
              <th class="text-left p-3 border-b">Tarifa</th>
              <th class="text-left p-3 border-b">Estatus</th>
              <th class="text-left p-3 border-b">Atraso (días)</th>
              <th class="text-left p-3 border-b">Cargo estimado</th>
              <th class="text-left p-3 border-b">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($paginator as $r)
              @php
                $rentalAt   = \Carbon\Carbon::parse($r->rental_date);
                $dueAt      = (clone $rentalAt)->addDays((int)$r->rental_duration);
                $returnedAt = $r->return_date ? \Carbon\Carbon::parse($r->return_date) : null;

                // atraso
                if ($returnedAt) {
                    $lateDays = max(0, $returnedAt->startOfDay()->diffInDays($dueAt->startOfDay(), false) * -1);
                } else {
                    $lateDays = max(0, \Carbon\Carbon::now()->startOfDay()->diffInDays($dueAt->startOfDay(), false) * -1);
                }
                $lateFee  = $lateDays * $dailyLateFee;

                // estatus
                if ($returnedAt) {
                    $status = 'Devuelta';
                    $statusClass = 'text-gray-700';
                } else {
                    if ($lateDays > 0) {
                        $status = 'Vencida';
                        $statusClass = 'text-red-600 font-semibold';
                    } else {
                        $status = 'En curso';
                        $statusClass = 'text-green-700 font-semibold';
                    }
                }
              @endphp

              <tr>
                <td class="p-3 align-top">{{ $r->rental_id }}</td>
                <td class="p-3 align-top">
                  <a class="text-blue-700 hover:underline" href="{{ route('customer.films.show', $r->film_id) }}">
                    {{ $r->title }}
                  </a>
                </td>
                <td class="p-3 align-top">{{ $rentalAt->format('Y-m-d H:i') }}</td>
                <td class="p-3 align-top">{{ $dueAt->format('Y-m-d H:i') }}</td>
                <td class="p-3 align-top">
                  {{ $returnedAt ? $returnedAt->format('Y-m-d H:i') : '—' }}
                </td>
                <td class="p-3 align-top">#{{ $r->store_id }}</td>
                <td class="p-3 align-top">{{ $r->rental_duration }} día(s)</td>
                <td class="p-3 align-top">${{ number_format((float)$r->rental_rate, 2) }}</td>
                <td class="p-3 align-top">
                  <span class="{{ $statusClass }}">{{ $status }}</span>
                </td>
                <td class="p-3 align-top">{{ $lateDays }}</td>
                <td class="p-3 align-top">${{ number_format($lateFee, 2) }}</td>
                <td class="p-3 align-top">
                  <a class="text-sm text-blue-700 hover:underline" href="{{ route('customer.films.show', $r->film_id) }}">Ver película</a>
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
