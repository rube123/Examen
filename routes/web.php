<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;   
use App\Http\Controllers\Customer\CatalogController;       
use App\Http\Controllers\Customer\AccountController;       
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEmpleadoController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\AdminCuentaController;


Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (invitan al guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Utilidad
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

    return response()->json([
        'user' => $user->only(['id','name','email']),
        'role' => $role,
        'mensaje' => "Has iniciado sesión como: {$label}",
    ]);
})->name('whoami');

// Rutas CUSTOMER (solo un grupo, no dupliques)
Route::middleware(['auth','verified','role:customer'])
    ->prefix('customer')->name('customer.')
    ->group(function () {

        // Catálogo
        Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');
        Route::get('films/{filmId}', [CatalogController::class, 'show'])->name('films.show');

        // Cuenta
        Route::get('rentals', [AccountController::class, 'rentalsHistory'])->name('rentals');
        Route::get('payments', [AccountController::class, 'payments'])->name('payments');
        Route::get('charges/pending', [AccountController::class, 'pendingCharges'])->name('charges');
    });
// Cargar rutas de autenticación si existen
if (file_exists(base_path('routes/auth.php'))) {
    require base_path('routes/auth.php');
}

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

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/empleados', [AdminController::class, 'empleados'])->name('admin.empleados');
    Route::get('/admin/peliculas', [AdminController::class, 'catalogo'])->name('admin.peliculas');
    Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/empleados', [AdminEmpleadoController::class, 'index'])->name('empleados');
    Route::get('/empleados/crear', [AdminEmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [AdminEmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{id}/editar', [AdminEmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [AdminEmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [AdminEmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/tiendas', [AdminStoreController::class, 'index'])->name('admin.tiendas');
    Route::get('/tiendas/crear', [AdminStoreController::class, 'create'])->name('admin.tiendas.create');
    Route::post('/tiendas', [AdminStoreController::class, 'store'])->name('admin.tiendas.store');
    Route::get('/tiendas/{id}/editar', [AdminStoreController::class, 'edit'])->name('admin.tiendas.edit');
    Route::put('/tiendas/{id}', [AdminStoreController::class, 'update'])->name('admin.tiendas.update');
    Route::delete('/tiendas/{id}', [AdminStoreController::class, 'destroy'])->name('admin.tiendas.destroy');
});

Route::get('/admin/tiendas', [AdminStoreController::class, 'index'])->name('admin.tiendas');

Route::get('/admin/cuentas', [AdminCuentaController::class, 'index'])->name('admin.cuentas');
Route::post('/admin/cuentas/{id}/reset', [AdminCuentaController::class, 'resetPassword'])->name('admin.cuentas.reset');
Route::post('/admin/cuentas/{id}/toggle', [AdminCuentaController::class, 'toggleActive'])->name('admin.cuentas.toggle');


