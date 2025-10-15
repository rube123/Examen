<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->string('q')->toString();
        $query  = Staff::query()->orderBy('staff_id','desc');

        if ($buscar) {
            $query->where(function($q) use ($buscar){
                $q->where('first_name','like',"%$buscar%")
                  ->orWhere('last_name','like',"%$buscar%")
                  ->orWhere('email','like',"%$buscar%")
                  ->orWhere('username','like',"%$buscar%");
            });
        }

        $empleados = $query->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('empleados','buscar'));
    }

    public function create()
    {
        $tiendas = \DB::connection('sakila')->table('store')->orderBy('store_id')->get();
        return view('admin.employees.create', compact('tiendas'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'first_name' => ['required','string','max:45'],
            'last_name'  => ['required','string','max:45'],
            'email'      => ['nullable','email','max:50'],
            'username'   => ['required','string','max:16', Rule::unique('sakila.staff','username')],
            'store_id'   => ['required','integer','exists:sakila.store,store_id'],
            'active'     => ['required','boolean'],
        ]);

        // Password por defecto (staff tabla guarda hash plano)
        $datos['password'] = $datos['password'] ?? '1234'; // ajusta si quieres
        Staff::create($datos);

        return redirect()->route('admin.employees.index')->with('ok','Empleado creado.');
    }

    public function edit(int $id)
    {
        $empleado = Staff::findOrFail($id);
        $tiendas  = \DB::connection('sakila')->table('store')->orderBy('store_id')->get();
        return view('admin.employees.edit', compact('empleado','tiendas'));
    }

    public function update(Request $request, int $id)
    {
        $empleado = Staff::findOrFail($id);

        $datos = $request->validate([
            'first_name' => ['required','string','max:45'],
            'last_name'  => ['required','string','max:45'],
            'email'      => ['nullable','email','max:50'],
            'username'   => ['required','string','max:16', Rule::unique('sakila.staff','username')->ignore($empleado->staff_id,'staff_id')],
            'store_id'   => ['required','integer','exists:sakila.store,store_id'],
            'active'     => ['required','boolean'],
        ]);

        $empleado->update($datos);
        return redirect()->route('admin.employees.index')->with('ok','Empleado actualizado.');
    }

    public function destroy(int $id)
    {
        $empleado = Staff::findOrFail($id);
        // Política simple: marcar como inactivo en vez de borrar.
        $empleado->active = 0;
        $empleado->save();

        return redirect()->route('admin.employees.index')->with('ok','Empleado desactivado.');
    }
}
