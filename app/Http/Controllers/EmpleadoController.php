<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Rental;

class EmpleadoController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // ✅ Validar que el usuario tenga rol empleado
        if (!$user->isRole('empleado')) {
            return redirect()->route('dashboard')
                ->withErrors(['auth' => 'Solo los empleados pueden acceder a esta sección.']);
        }

        // Cargar clientes filtrados por la sucursal del empleado
        $clientes = Customer::where('store_id', $user->store_id ?? 1)->get();

        return view('empleado.dashboard', compact('user', 'clientes'));
    }

    public function registrarCliente(Request $request)
    {
        $user = Auth::user();

        if (!$user->isRole('empleado')) {
            return redirect()->route('dashboard')
                ->withErrors(['auth' => 'Acceso restringido a empleados.']);
        }

        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name'  => 'required|string|max:45',
            'email'      => 'nullable|email|unique:sakila.customer,email',
        ]);

        Customer::create([
            'store_id' => $user->store_id ?? 1,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'address_id' => 1,
            'active' => 1,
        ]);

        return redirect()->route('empleado.dashboard')
                         ->with('status', 'Cliente registrado correctamente.');
    }

    public function atrasados()
    {
        $user = Auth::user();

        if (!$user->isRole('empleado')) {
            return redirect()->route('dashboard')
                ->withErrors(['auth' => 'Solo los empleados pueden acceder a esta sección.']);
        }

        $atrasados = DB::connection('sakila')->select("
            SELECT CONCAT(c.last_name, ', ', c.first_name) AS customer,
                   a.phone, f.title
            FROM rental r
            INNER JOIN customer c ON r.customer_id = c.customer_id
            INNER JOIN address a ON c.address_id = a.address_id
            INNER JOIN inventory i ON r.inventory_id = i.inventory_id
            INNER JOIN film f ON i.film_id = f.film_id
            WHERE r.return_date IS NULL
              AND r.rental_date + INTERVAL f.rental_duration DAY < CURRENT_DATE()
            ORDER BY title
            LIMIT 10
        ");

        return view('empleado.atrasados', compact('atrasados'));
    }
}
