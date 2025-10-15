<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Nueva Tienda</h2>
  </x-slot>

  <div class="p-6 max-w-xl mx-auto bg-white rounded shadow">
    <form action="{{ route('admin.tiendas.store') }}" method="POST">
      @csrf
      <label class="block mb-2">Gerente:</label>
      <select name="manager_staff_id" class="w-full border rounded p-2 mb-4" required>
        <option value="">Seleccione...</option>
        @foreach($staff as $s)
          <option value="{{ $s->staff_id }}">{{ $s->first_name }} {{ $s->last_name }}</option>
        @endforeach
      </select>

      <label class="block mb-2">Dirección:</label>
      <select name="address_id" class="w-full border rounded p-2 mb-4" required>
        <option value="">Seleccione...</option>
        @foreach($direcciones as $d)
          <option value="{{ $d->address_id }}">{{ $d->address }}</option>
        @endforeach
      </select>

      <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
    </form>
  </div>
</x-app-layout>
