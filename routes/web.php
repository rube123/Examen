<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;   // <- IMPORTANTE
use App\Http\Controllers\Customer\CatalogController;       // <- IMPORTANTE
use App\Http\Controllers\Customer\AccountController;       // <- IMPORTANTE

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PeliculaController;

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PeliculaController;
>>>>>>> b9e1e6c (Comentarios)

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

