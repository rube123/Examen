<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrarse · Rentas for Movies</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-3xl mx-auto my-10 bg-white shadow rounded-lg">
    <div class="px-6 py-5 border-b">
      <h1 class="text-xl font-bold">Crear cuenta (Cliente)</h1>
    </div>

    @if ($errors->any())
      <div class="px-6 pt-5">
        <div class="rounded border border-red-200 bg-red-50 p-4">
          <div class="font-medium text-red-700 mb-2">Revisa estos campos:</div>
          <ul class="list-disc pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="px-6 py-5 space-y-6">
      @csrf

      {{-- Acceso --}}
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
          <input id="name" name="name" type="text" value="{{ old('name') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required autofocus>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
          <input id="password" name="password" type="password"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
          <input id="password_confirmation" name="password_confirmation" type="password"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>
      </div>

      <hr class="border-gray-200">

      {{-- Datos Sakila --}}
      <div class="grid md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
          <input id="address" name="address" type="text" value="{{ old('address') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>

        <div class="md:col-span-2">
          <label for="address2" class="block text-sm font-medium text-gray-700">Dirección 2 (opcional)</label>
          <input id="address2" name="address2" type="text" value="{{ old('address2') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2">
        </div>

        <div>
          <label for="district" class="block text-sm font-medium text-gray-700">Distrito/Estado</label>
          <input id="district" name="district" type="text" value="{{ old('district') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>

        <div>
          <label for="city_id" class="block text-sm font-medium text-gray-700">Ciudad</label>
          <select id="city_id" name="city_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            <option value="">Selecciona…</option>
            @foreach($cities as $c)
              <option value="{{ $c->city_id }}" @selected(old('city_id')==$c->city_id)>
                {{ $c->country }} — {{ $c->city }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="postal_code" class="block text-sm font-medium text-gray-700">Código Postal (opcional)</label>
          <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2">
        </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
          <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                 class="mt-1 block w-full border rounded-md px-3 py-2" required>
        </div>

        <div>
          <label for="store_id" class="block text-sm font-medium text-gray-700">Tienda</label>
          <select id="store_id" name="store_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            <option value="">Selecciona…</option>
            @foreach($stores as $s)
              <option value="{{ $s->store_id }}" @selected(old('store_id')==$s->store_id)>
                Sucursal #{{ $s->store_id }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="flex items-center justify-between">
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="text-sm text-blue-700 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
        @else
          <a href="/login" class="text-sm text-blue-700 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
        @endif

        <button type="submit"
                class="px-5 py-2.5 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">
          Registrarme
        </button>
      </div>
    </form>
  </div>
</body>
</html>
