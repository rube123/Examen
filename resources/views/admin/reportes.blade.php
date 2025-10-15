<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Reportes</h2></x-slot>

  <div class="p-6 grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-4">
      <h3 class="font-bold mb-3">Top 10 Películas más rentadas</h3>
      <ul class="list-disc ml-5">
        @foreach($rankingPeliculas as $r)
          <li>{{ $r->title }} — {{ $r->total }} rentas</li>
        @endforeach
      </ul>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
      <h3 class="font-bold mb-3">Top 10 Clientes con más rentas</h3>
      <ul class="list-disc ml-5">
        @foreach($clientesTop as $c)
          <li>{{ $c->cliente }} — {{ $c->total }} rentas</li>
        @endforeach
      </ul>
    </div>
  </div>
</x-app-layout>
