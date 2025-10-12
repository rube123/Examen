<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    /*public function dashboard()
    {
        $clientes = Customer::orderBy('first_name')->get();
        return view('empleado.dashboard', compact('clientes'));
    }*/
    public function dashboard()
    {
        $user = Auth::user();

        // Buscar la sucursal del empleado
        $staff = DB::table('staff')
            ->where('email', $user->email)
            ->select('store_id')
            ->first();

        if (!$staff) {
            return back()->withErrors(['error' => 'No se encontró la sucursal del empleado.']);
        }

        // Filtrar los clientes solo de esa sucursal
        $clientes = DB::table('customer')
            ->where('store_id', $staff->store_id)
            ->select('customer_id', 'first_name', 'last_name', 'email')
            ->get();


        return view('empleado.dashboard', compact('clientes', 'staff'));
    }



    public function atrasados()
    {
        // Consulta de rentas atrasadas basadas en la lógica de Sakila
        $atrasados = \DB::table('rental')
            ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
            ->join('address', 'customer.address_id', '=', 'address.address_id')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->select(
                'customer.customer_id',
                \DB::raw("CONCAT(customer.first_name, ' ', customer.last_name) AS cliente"),
                'address.phone',
                'film.title',
                'rental.rental_date',
                'film.rental_duration',
                'rental.return_date'
            )
            ->whereNull('rental.return_date')
            ->whereRaw('rental.rental_date + INTERVAL film.rental_duration DAY < CURRENT_DATE()')
            ->orderBy('film.title')
            ->get();

        return view('empleado.atrasados', compact('atrasados'));
    }


    // 🔹 Crear cliente nuevo (ya lo tienes)
    public function store(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50|unique:customer,email',
            'phone' => 'nullable|string|max:20',
        ]);

        // 🔹 Obtenemos el empleado actual según el email del usuario autenticado
        $empleado = \DB::table('staff')
            ->where('email', auth()->user()->email)
            ->select('store_id')
            ->first();

        if (!$empleado) {
            return back()->withErrors(['msg' => 'No se encontró la sucursal del empleado.']);
        }

        // 🔹 Insertamos una dirección mínima por requerimiento de la tabla customer
        $addressId = \DB::table('address')->insertGetId([
            'address' => 'Dirección genérica',
            'district' => 'Desconocido',
            'city_id' => 1,
            'postal_code' => '00000',
            'phone' => $request->phone ?? '0000000000',
            'last_update' => now(),
        ]);

        // 🔹 Insertamos el cliente con la sucursal del empleado autenticado
        \DB::table('customer')->insert([
            'store_id' => $empleado->store_id,  // <<--- AHORA NO SERÁ NULL
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address_id' => $addressId,
            'active' => 1,
            'create_date' => now(),
            'last_update' => now(),
        ]);

        return redirect()->back()->with('status', 'Cliente agregado exitosamente.');
    }
    
    // 🔹 Editar cliente
    public function edit($id)
    {
        $cliente = Customer::findOrFail($id);
        return view('empleado.editar_cliente', compact('cliente'));
    }

    // 🔹 Actualizar cliente
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50',
        ]);

        \DB::table('customer')
            ->where('customer_id', $id)
            ->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'last_update' => now(),
            ]);

        return redirect()->route('empleado.dashboard')->with('status', 'Cliente actualizado correctamente');
    }



    // 🔹 Eliminar cliente
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return back()->with('status', 'Cliente eliminado correctamente');
    }

    // 🔹 Historial de rentas
    public function historial($id)
    {
        $cliente = Customer::findOrFail($id);

        $rentas = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->select('rental.rental_id', 'film.title', 'rental.rental_date', 'rental.return_date')
            ->where('rental.customer_id', $id)
            ->orderByDesc('rental.rental_date')
            ->get();

        return view('empleado.historial', compact('cliente', 'rentas'));
    }
}
