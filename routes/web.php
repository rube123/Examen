<?php

use Illuminate\Support\Facades\Route;

// Controladores comunes / auth.
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;   // <- Formulario/acción de registro (si usas Breeze/tu propio Auth).
// CUSTOMER (lado cliente).
use App\Http\Controllers\Customer\CatalogController;       // <- Catálogo para clientes.
use App\Http\Controllers\Customer\AccountController;       // <- Cuenta/historial/cargos del cliente.
// ADMIN (panel de administración).
use App\Http\Controllers\Admin\AdminDashboardController;   // <- Dashboard Admin.
use App\Http\Controllers\Admin\StoreController;            // <- CRUD Tiendas.
use App\Http\Controllers\Admin\EmployeeController;         // <- CRUD Empleados.
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController; // <- CRUD Clientes (admin).
use App\Http\Controllers\Admin\Catalog1Controller;         // <- CRUD Catálogo .
use App\Http\Controllers\Admin\InventoryController;        // <- CRUD Inventario.
use App\Http\Controllers\Admin\ReportController;           // <- Reportes.
// EMPLEADO (módulo de empleados / sucursales).
use App\Http\Controllers\EmpleadoController;
// Historial de inventario (rutas bajo /empleado).
use App\Http\Controllers\InventoryHistoryController;    

// -----------------------------------------------------------------------------
// Home / Dashboard básico
// -----------------------------------------------------------------------------

Route::get('/', function () {
    return view('welcome'); // <- Página de bienvenida.
});

// Dashboard general (logueado + email verificado).
Route::get('/dashboard', function () {
    return view('dashboard'); // <- Vista de tu dashboard base.
})->middleware(['auth', 'verified'])->name('dashboard'); // <- Middleware según docs (verificación de email). 
// https://laravel.com/docs/12.x/verification :contentReference[oaicite:1]{index=1}

// -----------------------------------------------------------------------------
// Perfil del usuario (sección privada)
// -----------------------------------------------------------------------------

Route::middleware('auth')->group(function () { // <- Requiere estar autenticado.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');     // Editar perfil.
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Actualizar perfil.
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Borrar perfil.
});

// -----------------------------------------------------------------------------
// Registro (solo invitados/guests)
// -----------------------------------------------------------------------------

Route::middleware('guest')->group(function () { // <- Solo usuarios no autenticados.
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register'); // Form de registro.
    Route::post('/register', [RegisteredUserController::class, 'store']);                   // Acción de registro.
});

// -----------------------------------------------------------------------------
// Utilidad: ¿Quién soy? (debug rápido del rol)
// -----------------------------------------------------------------------------

Route::middleware(['auth'])->get('/whoami', function () {
    $user = auth()->user();
    $role = optional($user->role)->name; // <- role_id -> roles.name (esquema propio).

    $map = [
        'admin'    => 'Administrador general',
        'employee' => 'Empleado de sucursal',
        'customer' => 'Cliente',
        'public'   => 'Público general',
    ];

    $label = $map[$role] ?? 'Rol no asignado';

    return response()->json([
        'user'    => $user->only(['id','name','email']),
        'role'    => $role,
        'mensaje' => "Has iniciado sesión como: {$label}",
    ]);
})->name('whoami');

// -----------------------------------------------------------------------------
// CUSTOMER (cliente final)
// -----------------------------------------------------------------------------

Route::middleware(['auth','verified'])     // <- Añade aquí 'customer' si creas tu middleware propio.
    ->prefix('customer')                   // <- URL: /customer/...
    ->name('customer.')                    // <- Nombres: customer.*
    ->group(function () {

        // Catálogo del cliente.
        Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');      // GET /customer/catalog
        Route::get('films/{filmId}', [CatalogController::class, 'show'])->name('films.show');

        // Cuenta del cliente.
        Route::get('rentals', [AccountController::class, 'rentalsHistory'])->name('rentals');
        Route::get('payments', [AccountController::class, 'payments'])->name('payments');
        Route::get('charges/pending', [AccountController::class, 'pendingCharges'])->name('charges');
    });

// -----------------------------------------------------------------------------
// ADMIN (panel de administración)
// -----------------------------------------------------------------------------

Route::middleware(['auth','admin'])        // <- Requiere tu middleware 'admin' (EnsureAdmin).
    ->prefix('admin')                      // <- URL: /admin/...
    ->name('admin.')                       // <- Nombres: admin.*
    ->group(function () {

        // Dashboard del administrador.
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUDs principales (usa resource controllers para ahorrar rutas).
        // https://laravel.com/docs/12.x/controllers#resource-controllers
        Route::resources([
            'stores'    => StoreController::class,              // admin/stores/*
            'employees' => EmployeeController::class,           // admin/employees/*
            'customers' => AdminCustomerController::class,      // admin/customers/*
            'catalog'   => Catalog1Controller::class,           // admin/catalog/*
            'inventory' => InventoryController::class,          // admin/inventory/*
        ]);

        // Reportes (ejemplos de índices/exports).
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
        Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

// -----------------------------------------------------------------------------
// EMPLEADO (módulo de empleados / sucursal)
// -----------------------------------------------------------------------------

Route::prefix('empleado')                  // <- URL: /empleado/...
    ->middleware(['auth','verified'])      // <- Añade 'employee' si creas middleware propio.
    ->name('empleado.')                    // <- Nombres: empleado.*
    ->group(function () {

        // Dashboard del empleado.
        Route::get('/', [EmpleadoController::class, 'dashboard'])->name('dashboard');

        // Gestión de clientes (empleado).
        Route::post('/clientes', [EmpleadoController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{id}/edit', [EmpleadoController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{id}', [EmpleadoController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{id}', [EmpleadoController::class, 'destroy'])->name('clientes.destroy');
        Route::get('/clientes/{id}/historial', [EmpleadoController::class, 'historial'])->name('clientes.historial');

        // Listas de atrasos / moras.
        Route::get('/atrasados', [EmpleadoController::class, 'atrasados'])->name('atrasados');

        // Módulo de películas para empleado.
        Route::get('/peliculas', [EmpleadoController::class, 'peliculas'])->name('peliculas');                   // Listado.
        Route::put('/peliculas/{id}/marcar', [EmpleadoController::class, 'marcarPelicula'])->name('peliculas.marcar'); // Marcar.
        Route::get('/peliculas/{id}/historial', [EmpleadoController::class, 'historialPelicula'])->name('peliculas.historial');

        // Rentas (empleado).
        Route::get('/rentas', [EmpleadoController::class, 'rentas'])->name('rentas');                    // Mostrar rentas.
        Route::post('/rentas', [EmpleadoController::class, 'storeRenta'])->name('rentas.store');         // Crear renta.
        Route::put('/rentas/{id}/devolver', [EmpleadoController::class, 'devolver'])->name('rentas.devolver'); // Devolver renta.
        Route::get('/rentas/cargos', [EmpleadoController::class, 'calcularCargos'])->name('rentas.cargos');    // Calcular cargos.

        // Historial de inventario (empleado).
        Route::get('/historial/{inventory_id}', [InventoryHistoryController::class, 'show'])->name('historial');       // Ver historial de un item.
        Route::post('/historial', [InventoryHistoryController::class, 'store'])->name('historial.store');              // Registrar evento en historial.
    });


    // ----- ADMIN: CRUD y utilidades -----
    Route::middleware(['auth','verified','admin'])
        ->prefix('admin')->name('admin.')
        ->group(function () {

        // Dashboard de administrador (/admin)
        Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Gestión global
        Route::resource('stores',    \App\Http\Controllers\Admin\StoreController::class)->except(['show']);
        Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class)->except(['show']);
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->except(['show']);

        // Usuarios de la app (reset password / bloquear)
        Route::get ('users',              [\App\Http\Controllers\Admin\UserAdminController::class, 'index'])->name('users.index');
        Route::post('users/{id}/reset',   [\App\Http\Controllers\Admin\UserAdminController::class, 'resetPassword'])->name('users.reset');
        Route::post('users/{id}/block',   [\App\Http\Controllers\Admin\UserAdminController::class, 'block'])->name('users.block');
        Route::post('users/{id}/unblock', [\App\Http\Controllers\Admin\UserAdminController::class, 'unblock'])->name('users.unblock');

        // Catálogo
        Route::resource('films',      \App\Http\Controllers\Admin\FilmController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
        Route::resource('languages',  \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);

        // Inventario (copias por tienda)
        Route::resource('inventory', \App\Http\Controllers\Admin\InventoryController::class)
            ->only(['index','create','store','destroy']);

        // Importación OMDb (API externa)
        Route::post('films/import/omdb', [\App\Http\Controllers\Admin\FilmController::class, 'importOmdb'])
            ->name('films.import.omdb');

        // Reportes
        Route::get('reports',      [\App\Http\Controllers\Admin\ReportController::class,'index'])->name('reports.index');  // vista
        Route::get('reports/csv',  [\App\Http\Controllers\Admin\ReportController::class,'exportCsv'])->name('reports.csv'); // CSV
        Route::get('reports/pdf',  [\App\Http\Controllers\Admin\ReportController::class,'exportPdf'])->name('reports.pdf'); // PDF
    });


// -----------------------------------------------------------------------------
// Carga de rutas de autenticación generadas por scaffolding (Breeze/Jetstream/etc.)
// -----------------------------------------------------------------------------

if (file_exists(base_path('routes/auth.php'))) { // <- Solo si existe el archivo.
    require base_path('routes/auth.php');        // <- Trae login/logout/forgot/verify, etc., según tu scaffolding.
}

// -----------------------------------------------------------------------------
// Tip: lista todas tus rutas en consola con:
// php artisan route:list
// -----------------------------------------------------------------------------
