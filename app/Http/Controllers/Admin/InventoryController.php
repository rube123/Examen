<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $store_id = $request->integer('store_id');
        $q = DB::connection('sakila')->table('inventory as i')
            ->join('film as f','f.film_id','=','i.film_id')
            ->select('i.inventory_id','i.store_id','i.film_id','f.title')
            ->when($store_id, fn($x)=>$x->where('i.store_id',$store_id))
            ->orderBy('i.inventory_id','desc');

        $inventario = $q->paginate(20)->withQueryString();
        $tiendas = DB::connection('sakila')->table('store')->get();

        return view('admin.inventory.index', compact('inventario','tiendas','store_id'));
    }

    public function create()
    {
        $tiendas = DB::connection('sakila')->table('store')->get();
        $films   = DB::connection('sakila')->table('film')->select('film_id','title')->orderBy('title')->get();
        return view('admin.inventory.create', compact('tiendas','films'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'film_id'  => ['required','integer','exists:sakila.film,film_id'],
            'store_id' => ['required','integer','exists:sakila.store,store_id'],
            'cantidad' => ['required','integer','min:1','max:1000'],
        ]);

        // Inserta N copias
        for ($i=0; $i<$datos['cantidad']; $i++) {
            Inventory::create([
                'film_id'  => $datos['film_id'],
                'store_id' => $datos['store_id'],
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('ok','Copias agregadas.');
    }

    public function destroy(int $id)
    {
        Inventory::where('inventory_id',$id)->delete();
        return back()->with('ok','Copia eliminada.');
    }
}
