<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeliculaController extends Controller
{
    // --- Mostrar inventario filtrable ---
    public function index(Request $request)
    {
        $storeId = DB::table('staff')
            ->where('email', Auth::user()->email)
            ->value('store_id');

        $query = DB::table('inventory as i')
            ->join('film as f', 'i.film_id', '=', 'f.film_id')
            ->join('language as l', 'f.language_id', '=', 'l.language_id')
            ->leftJoin('film_category as fc', 'f.film_id', '=', 'fc.film_id')
            ->leftJoin('category as c', 'fc.category_id', '=', 'c.category_id')
            ->where('i.store_id', $storeId)
            ->select('i.inventory_id', 'f.title', 'l.name as language', 'c.name as category', 'i.last_update');

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('f.title', 'like', $term)
                  ->orWhere('c.name', 'like', $term)
                  ->orWhere('l.name', 'like', $term);
            });
        }

        $peliculas = $query->orderBy('f.title')->paginate(10);

        return view('peliculas.index', compact('peliculas'));
    }

    // --- Historial de una copia ---
    public function historial($inventory_id)
    {
        $historial = DB::table('rental as r')
            ->join('customer as c', 'r.customer_id', '=', 'c.customer_id')
            ->select('r.rental_date', 'r.return_date', 'c.first_name', 'c.last_name')
            ->where('r.inventory_id', $inventory_id)
            ->orderByDesc('r.rental_date')
            ->get();

        return view('peliculas.historial', compact('historial'));
    }

    // --- Marcar copia como dañada/perdida ---
    public function marcar($inventory_id, Request $request)
    {
        $status = $request->status; // 'dañada' o 'perdida'

        DB::table('inventory')
            ->where('inventory_id', $inventory_id)
            ->update([
                'last_update' => now(),
            ]);

        DB::table('inventory_log')->insert([
            'inventory_id' => $inventory_id,
            'status' => $status,
            'created_at' => now(),
        ]);

        return redirect()->route('empleado.peliculas.index')
            ->with('status', "La copia $inventory_id fue marcada como $status.");
    }
}
