<?php

use Illuminate\Support\Facades\Route;

// ---------------------- Controladores comunes / auth ----------------------
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

// ---------------------- CUSTOMER (lado cliente) ---------------------------
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\AccountController;

// ---------------------- ADMIN (panel de administración) -------------------
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserAdminController;

// ---------------------- EMPLEADO (sucursal) --------------------------------
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\InventoryHistoryController;


// -----------------------------------------------------------------------------
// Home / Dashboard básico
// -----------------------------------------------------------------------------
Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// -----------------------------------------------------------------------------
// Perfil del usuario (privado)
// -----------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});


// -----------------------------------------------------------------------------
// Registro (solo invitados)
// -----------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});


// -----------------------------------------------------------------------------
// Utilidad: ¿Quién soy? (debug rápido del rol)
// -----------------------------------------------------------------------------
Route::middleware(['auth'])->get('/whoami', function () {
    $user = auth()->user();
    $role = optional($user->role)->name;

    $labels = [
        'admin'    => 'Administrador general',
        'employee' => 'Empleado de sucursal',
        'customer' => 'Cliente',
        'public'   => 'Público general',
    ];

    return response()->json([
        'user'    => $user->only(['id','name','email']),
        'role'    => $role,
        'mensaje' => 'Has iniciado sesión como: ' . ($labels[$role] ?? 'Rol no asignado'),
    ]);
})->name('whoami');


// -----------------------------------------------------------------------------
// CUSTOMER (cliente final)
// -----------------------------------------------------------------------------
Route::middleware(['auth','notblocked','verified'])
    ->prefix('customer')->name('customer.')
    ->group(function () {
        // Catálogo
        Route::get('catalog',          [CatalogController::class, 'index'])->name('catalog');
        Route::get('films/{filmId}',   [CatalogController::class, 'show'])->name('films.show');

        // Cuenta
        Route::get('rentals',          [AccountController::class, 'rentalsHistory'])->name('rentals');
        Route::get('payments',         [AccountController::class, 'payments'])->name('payments');
        Route::get('charges/pending',  [AccountController::class, 'pendingCharges'])->name('charges');
    });


// -----------------------------------------------------------------------------
// ADMIN (panel de administración)
// -----------------------------------------------------------------------------
Route::middleware(['auth','notblocked','verified','admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestión global (CRUDs)
        Route::resources([
            'stores'    => StoreController::class,             // admin/stores/*
            'employees' => EmployeeController::class,          // admin/employees/*
            'customers' => AdminCustomerController::class,     // admin/customers/*
        ]);

        // Usuarios de la app (reset / bloquear)
        Route::get ('users',             [UserAdminController::class, 'index'])->name('users.index');
        Route::post('users/{id}/reset',  [UserAdminController::class, 'resetPassword'])->name('users.reset');
        Route::post('users/{id}/block',  [UserAdminController::class, 'block'])->name('users.block');
        Route::post('users/{id}/unblock',[UserAdminController::class, 'unblock'])->name('users.unblock');

        // Catálogo
        Route::resource('films',      FilmController::class);                     // admin/films/*
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('languages',  LanguageController::class)->except(['show']);

        // Inventario (copias por tienda)
        Route::resource('inventory',  InventoryController::class)
            ->only(['index','create','store','destroy']);

        // Importación OMDb (API externa)
        Route::post('films/import/omdb', [FilmController::class, 'importOmdb'])
            ->name('films.import.omdb');

        // Reportes
        Route::get('reports',     [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
        Route::get('reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });


// -----------------------------------------------------------------------------
// EMPLEADO (módulo de sucursal)
// -----------------------------------------------------------------------------
Route::middleware(['auth','notblocked','verified'])
    ->prefix('empleado')->name('empleado.')
    ->group(function () {

        // Dashboard del empleado
        Route::get('/', [EmpleadoController::class, 'dashboard'])->name('dashboard');

        // Gestión de clientes por empleado
        Route::post('/clientes',                [EmpleadoController::class, 'store'])->name('clientes.store');
        Route::get ('/clientes/{id}/edit',      [EmpleadoController::class, 'edit'])->name('clientes.edit');
        Route::put ('/clientes/{id}',           [EmpleadoController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{id}',         [EmpleadoController::class, 'destroy'])->name('clientes.destroy');
        Route::get ('/clientes/{id}/historial', [EmpleadoController::class, 'historial'])->name('clientes.historial');

        // Atrasados / moras
        Route::get('/atrasados', [EmpleadoController::class, 'atrasados'])->name('atrasados');

        // Películas (empleado)
        Route::get ('/peliculas',              [EmpleadoController::class, 'peliculas'])->name('peliculas');
        Route::put ('/peliculas/{id}/marcar',  [EmpleadoController::class, 'marcarPelicula'])->name('peliculas.marcar');
        Route::get ('/peliculas/{id}/historial',[EmpleadoController::class, 'historialPelicula'])->name('peliculas.historial');

        // Rentas
        Route::get ('/rentas',                 [EmpleadoController::class, 'rentas'])->name('rentas');
        Route::post('/rentas',                 [EmpleadoController::class, 'storeRenta'])->name('rentas.store');
        Route::put ('/rentas/{id}/devolver',   [EmpleadoController::class, 'devolver'])->name('rentas.devolver');
        Route::get ('/rentas/cargos',          [EmpleadoController::class, 'calcularCargos'])->name('rentas.cargos');

        // Historial de inventario
        Route::get ('/historial/{inventory_id}', [InventoryHistoryController::class, 'show'])->name('historial');
        Route::post('/historial',                [InventoryHistoryController::class, 'store'])->name('historial.store');
    });


// -----------------------------------------------------------------------------
// Carga de rutas de autenticación generadas por scaffolding (Breeze/Jetstream)
// -----------------------------------------------------------------------------
if (file_exists(base_path('routes/auth.php'))) {
    require base_path('routes/auth.php');
}
