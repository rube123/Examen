<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStoreController extends Controller
{
    // Listar tiendas
    public function index()
{
    $tiendas = DB::table('store')
        ->join('address', 'store.address_id', '=', 'address.address_id')
        ->leftJoin('staff', 'store.manager_staff_id', '=', 'staff.staff_id')
        ->select(
            'store.store_id',
            DB::raw('CONCAT(staff.first_name, " ", staff.last_name) as manager'),
            'address.address',
            'store.last_update'
        )
        ->orderBy('store.store_id')
        ->get();

    return view('admin.tiendas', compact('tiendas'));
}


    // Mostrar formulario de creación
    public function create()
    {
        $staff = DB::table('staff')->select('staff_id', 'first_name', 'last_name')->get();
        $direcciones = DB::table('address')->select('address_id', 'address')->get();

        return view('admin.tiendas_crear', compact('staff', 'direcciones'));
    }

    // Guardar nueva tienda
    public function store(Request $request)
    {
        $request->validate([
            'manager_staff_id' => 'required|integer',
            'address_id' => 'required|integer'
        ]);

        DB::table('store')->insert([
            'manager_staff_id' => $request->manager_staff_id,
            'address_id' => $request->address_id,
            'last_update' => now()
        ]);

        return redirect()->route('admin.tiendas')->with('status', 'Tienda creada exitosamente.');
    }

    public function edit($id)
    {
        $tienda = DB::table('store')
            ->join('address', 'store.address_id', '=', 'address.address_id')
            ->select('store.store_id', 'address.address', 'store.manager_staff_id', 'store.last_update')
            ->where('store.store_id', $id)
            ->first();

        return view('admin.tiendas_edit', compact('tienda'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|string|max:100',
            'manager_staff_id' => 'required|integer',
        ]);

        // Actualizamos la dirección
        DB::table('store')
            ->join('address', 'store.address_id', '=', 'address.address_id')
            ->where('store.store_id', $id)
            ->update([
                'address.address' => $request->address,
                'store.manager_staff_id' => $request->manager_staff_id,
                'store.last_update' => now(),
            ]);

        return redirect()->route('admin.tiendas')->with('status', 'Tienda actualizada correctamente.');
    }


    // Eliminar tienda
    public function destroy($id)
    {
        DB::table('store')->where('store_id', $id)->delete();
        return redirect()->route('admin.tiendas')->with('status', 'Tienda eliminada correctamente.');
    }
}
