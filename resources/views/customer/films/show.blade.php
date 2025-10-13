<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Detalle de película
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto p-6">
    <a href="{{ route('customer.catalog') }}" class="text-blue-600 hover:underline">← Volver al catálogo</a>

    <h1 class="text-2xl font-bold mt-2 mb-1">{{ $film->title }}</h1>
    <div class="text-gray-600 mb-4">
      {{ $film->language }} · {{ $film->length }} min · ${{ number_format($film->rental_rate,2) }}
    </div>

    <p class="mb-6">{{ $film->description }}</p>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h3 class="font-semibold mb-2">Actores</h3>
        <ul class="list-disc pl-5">
          @forelse($actors as $a)
            <li>{{ $a->first_name }} {{ $a->last_name }}</li>
          @empty
            <li class="text-gray-500">Sin actores registrados.</li>
          @endforelse
        </ul>

        <h3 class="font-semibold mt-6 mb-2">Categorías</h3>
        <ul class="list-disc pl-5">
          @forelse($categories as $c)
            <li>{{ $c->name }}</li>
          @empty
            <li class="text-gray-500">Sin categorías.</li>
          @endforelse
        </ul>
      </div>

      <div>
        <h3 class="font-semibold mb-2">Disponibilidad por sucursal</h3>
        <table class="w-full border">
          <thead>
            <tr class="bg-gray-100">
              <th class="text-left p-2 border">Tienda</th>
              <th class="text-left p-2 border">Disponibles</th>
            </tr>
          </thead>
          <tbody>
            @foreach($availability as $row)
              <tr>
                <td class="p-2 border">#{{ $row->store_id }}</td>
                <td class="p-2 border {{ $row->available_now > 0 ? 'text-green-700' : 'text-red-600' }}">
                  {{ $row->available_now }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
