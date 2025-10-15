<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminEmpleadoController extends Controller
{
    public function index()
    {
        $empleados = DB::table('staff')
            ->join('store', 'staff.store_id', '=', 'store.store_id')
            ->select('staff.staff_id', 'staff.first_name', 'staff.last_name', 'staff.email', 'store.store_id')
            ->get();

        return view('admin.empleados', compact('empleados'));
    }

    public function create()
    {
        $tiendas = DB::table('store')->select('store_id')->get();
        return view('admin.empleados_create', compact('tiendas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email|unique:staff,email',
            'store_id' => 'required|integer',
            'password' => 'required|min:6',
        ]);

        DB::table('staff')->insert([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address_id' => 1, // puedes adaptarlo si usas direcciones
            'email' => $request->email,
            'store_id' => $request->store_id,
            'active' => 1,
            'username' => strtolower($request->first_name),
            'password' => Hash::make($request->password),
            'last_update' => now(),
        ]);

        return redirect()->route('admin.empleados')->with('status', 'Empleado agregado correctamente.');
    }

    public function edit($id)
    {
        $empleado = DB::table('staff')->where('staff_id', $id)->first();
        $tiendas = DB::table('store')->select('store_id')->get();
        return view('admin.empleados_edit', compact('empleado', 'tiendas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'required|email',
            'store_id' => 'required|integer',
        ]);

        DB::table('staff')->where('staff_id', $id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'store_id' => $request->store_id,
            'last_update' => now(),
        ]);

        return redirect()->route('admin.empleados')->with('status', 'Empleado actualizado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('staff')->where('staff_id', $id)->delete();
        return redirect()->route('admin.empleados')->with('status', 'Empleado eliminado correctamente.');
    }
}
