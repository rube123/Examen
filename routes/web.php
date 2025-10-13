<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PeliculaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::middleware(['auth'])->get('/whoami', function () {
    $user = auth()->user();
    $role = optional($user->role)->name;

    $map = [
        'admin'    => 'Administrador general',
        'employee' => 'Empleado de sucursal',
        'customer' => 'Cliente',
        'public'   => 'Público general',
    ];

    $label = $map[$role] ?? 'Rol no asignado';

    // Puedes renderizar una vista Blade o responder JSON.
    return response()->json([
        'user' => $user->only(['id','name','email']),
        'role' => $role,
        'mensaje' => "Has iniciado sesión como: {$label}",
    ]);
})->name('whoami');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/empleado', [EmpleadoController::class, 'dashboard'])->name('empleado.dashboard');

    Route::post('/empleado/clientes', [EmpleadoController::class, 'store'])->name('empleado.clientes.store');
    Route::get('/empleado/clientes/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleado.clientes.edit');
    Route::put('/empleado/clientes/{id}', [EmpleadoController::class, 'update'])->name('empleado.clientes.update');
    Route::delete('/empleado/clientes/{id}', [EmpleadoController::class, 'destroy'])->name('empleado.clientes.destroy');

    Route::get('/empleado/clientes/{id}/historial', [EmpleadoController::class, 'historial'])->name('empleado.clientes.historial');

    Route::get('/empleado/atrasados', [EmpleadoController::class, 'atrasados'])
        ->name('empleado.atrasados');
    
    Route::put('/empleado/clientes/{id}/update', [EmpleadoController::class, 'update'])
        ->name('empleado.clientes.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/empleado', [EmpleadoController::class, 'dashboard'])->name('empleado.dashboard');
    Route::get('/empleado/atrasados', [EmpleadoController::class, 'atrasados'])->name('empleado.atrasados');

    // 🔹 Nueva ruta para el módulo de películas
    Route::get('/empleado/peliculas', [EmpleadoController::class, 'peliculas'])->name('empleado.peliculas');

    Route::put('/empleado/peliculas/{id}/marcar', [EmpleadoController::class, 'marcarPelicula'])->name('empleado.peliculas.marcar');
    Route::get('/empleado/peliculas/{id}/historial', [EmpleadoController::class, 'historialPelicula'])->name('empleado.peliculas.historial');

});

// Rentas
Route::get('/empleado/rentas', [EmpleadoController::class, 'rentas'])->name('empleado.rentas');
Route::post('/empleado/rentas', [EmpleadoController::class, 'storeRenta'])->name('empleado.rentas.store');
Route::put('/empleado/rentas/{id}/devolver', [EmpleadoController::class, 'devolver'])->name('empleado.rentas.devolver');
Route::get('/empleado/rentas/cargos', [EmpleadoController::class, 'calcularCargos'])->name('empleado.rentas.cargos');


Route::get('/empleado/rentas', [EmpleadoController::class, 'rentas'])
    ->name('empleado.rentas')
    ->middleware(['auth', 'verified']);

    Route::get('/empleado/rentas', [EmpleadoController::class, 'rentas'])
    ->name('empleado.rentas');

Route::get('/empleado/rentas', [EmpleadoController::class, 'rentas'])->name('empleado.rentas');
Route::post('/empleado/rentas', [EmpleadoController::class, 'storeRenta'])->name('empleado.rentas.store');
Route::put('/empleado/rentas/{id}/devolver', [EmpleadoController::class, 'devolver'])->name('empleado.rentas.devolver');

Route::prefix('empleado')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/historial/{inventory_id}', [App\Http\Controllers\InventoryHistoryController::class, 'show'])
        ->name('empleado.historial');

    Route::post('/historial', [App\Http\Controllers\InventoryHistoryController::class, 'store'])
        ->name('empleado.historial.store');
});


