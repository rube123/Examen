<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
