<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;   // <- IMPORTANTE
use App\Http\Controllers\Customer\CatalogController;       // <- IMPORTANTE
use App\Http\Controllers\Customer\AccountController;       // <- IMPORTANTE

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
