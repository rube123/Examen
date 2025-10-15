<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminCuentaController extends Controller
{
    public function index()
    {
        $empleados = DB::table('staff')
            ->select('staff_id', 'first_name', 'last_name', 'email', 'active', 'last_update')
            ->get();

        return view('admin.cuentas', compact('empleados'));
    }

    public function resetPassword($id)
    {
        DB::table('staff')
            ->where('staff_id', $id)
            ->update([
                'password' => Hash::make('empleado123'),
                'last_update' => now(),
            ]);

        return back()->with('status', 'Contraseña reseteada correctamente. Nueva: empleado123');
    }

    public function toggleActive($id)
    {
        $empleado = DB::table('staff')->where('staff_id', $id)->first();
        $nuevoEstado = $empleado->active ? 0 : 1;

        DB::table('staff')->where('staff_id', $id)->update([
            'active' => $nuevoEstado,
            'last_update' => now(),
        ]);

        $mensaje = $nuevoEstado
            ? 'Cuenta reactivada correctamente.'
            : 'Cuenta bloqueada correctamente.';

        return back()->with('status', $mensaje);
    }
}
