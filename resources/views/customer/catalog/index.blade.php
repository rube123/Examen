<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Catálogo
    </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto p-6">
    {{-- Filtros --}}
    <form method="GET" class="grid md:grid-cols-6 gap-3 bg-gray-50 p-4 rounded border mb-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Título</label>
        <input type="text" name="title" value="{{ $title ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="Buscar por título...">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tienda</label>
        <select name="store_id" class="w-full border rounded px-3 py-2">
          <option value="">Todas</option>
          @foreach($stores as $s)
            <option value="{{ $s->store_id }}" @selected(($storeId ?? null)==$s->store_id)>{{ $s->store_id }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Categoría</label>
        <select name="category_id" class="w-full border rounded px-3 py-2">
          <option value="">Todas</option>
          @foreach($categories as $c)
            <option value="{{ $c->category_id }}" @selected(($categoryId ?? null)==$c->category_id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Idioma</label>
        <select name="language_id" class="w-full border rounded px-3 py-2">
          <option value="">Todos</option>
          @foreach($languages as $l)
            <option value="{{ $l->language_id }}" @selected(($languageId ?? null)==$l->language_id)>{{ $l->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="flex items-end">
        <label class="inline-flex items-center">
          <input type="checkbox" name="available_only" value="1" class="mr-2" @checked(!empty($availableOnly))>
          Solo disponibles ahora
        </label>
      </div>

      <div class="md:col-span-6 text-right">
        <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg
                       bg-blue-600 text-white hover:bg-blue-700
                       focus:outline-none focus:ring-2 focus:ring-blue-600/40 shadow-sm">
          Buscar
        </button>
      </div>
    </form>

    {{-- Resultados --}}
    @if($films->count() === 0)
      <div class="text-gray-600">No hay resultados.</div>
    @else
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($films as $f)
          <div class="border rounded p-4 bg-white shadow-sm">
            <h3 class="font-semibold text-lg">
              <a class="text-blue-700 hover:underline" href="{{ route('customer.films.show', $f->film_id) }}">
                {{ $f->title }}
              </a>
            </h3>
            <div class="text-sm text-gray-600">
              {{ $f->language }} · {{ $f->length }} min · ${{ number_format($f->rental_rate,2) }}
              @isset($f->available_now)
                · <span class="{{ ($f->available_now ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">
                  {{ $f->available_now ?? 0 }} disp.
                </span>
              @endisset
            </div>
            <p class="mt-2 text-sm text-gray-700 line-clamp-3">{{ $f->description }}</p>
            <div class="mt-3">
              <a class="text-sm text-blue-600 hover:underline" href="{{ route('customer.films.show', $f->film_id) }}">
                Ver detalle »
              </a>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $films->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
