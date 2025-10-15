<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Catálogo de Películas</h2></x-slot>

  <div class="p-6 bg-white rounded-lg shadow">
    <table class="w-full table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2 text-left">Título</th>
          <th class="p-2 text-left">Año</th>
          <th class="p-2 text-left">Rating</th>
        </tr>
      </thead>
      <tbody>
        @foreach($peliculas as $p)
        <tr class="border-t">
          <td class="p-2">{{ $p->film_id }}</td>
          <td class="p-2">{{ $p->title }}</td>
          <td class="p-2">{{ $p->release_year }}</td>
          <td class="p-2">{{ $p->rating }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-app-layout>
