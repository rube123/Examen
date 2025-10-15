<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalTiendas = DB::table('store')->count();
        $totalEmpleados = DB::table('staff')->count();
        $totalClientes = DB::table('customer')->count();
        $totalRentas = DB::table('rental')->count();

        return view('admin.dashboard', compact('totalTiendas', 'totalEmpleados', 'totalClientes', 'totalRentas'));
    }

    public function empleados()
    {
        $empleados = DB::table('staff')
            ->join('store', 'staff.store_id', '=', 'store.store_id')
            ->select('staff.staff_id', 'staff.first_name', 'staff.last_name', 'staff.email', 'store.store_id')
            ->get();

        return view('admin.empleados', compact('empleados'));
    }

    public function catalogo()
    {
        $peliculas = DB::table('film')
            ->join('language', 'film.language_id', '=', 'language.language_id')
            ->select('film.film_id', 'film.title', 'film.release_year', 'film.rating', 'language.name as idioma')
            ->limit(20)
            ->get();

        return view('admin.peliculas', compact('peliculas'));
    }

    public function reportes()
    {
        $ranking = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->select('film.title', DB::raw('COUNT(rental.rental_id) as total_rentas'))
            ->groupBy('film.title')
            ->orderByDesc('total_rentas')
            ->limit(10)
            ->get();

        return view('admin.reportes', compact('ranking'));
    }
}
