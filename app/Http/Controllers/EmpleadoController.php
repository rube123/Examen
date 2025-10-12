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

    public function peliculas(Request $request)
    {
        $email = Auth::user()->email;
        $staff = DB::table('staff')->where('email', $email)->first();

        if (!$staff) {
            return back()->withErrors(['error' => 'No se encontró la sucursal del empleado.']);
        }

        $query = DB::table('inventory')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->join('language', 'film.language_id', '=', 'language.language_id')
            ->leftJoin('film_category', 'film.film_id', '=', 'film_category.film_id')
            ->leftJoin('category', 'film_category.category_id', '=', 'category.category_id')
            ->leftJoin('film_actor', 'film.film_id', '=', 'film_actor.film_id')
            ->leftJoin('actor', 'film_actor.actor_id', '=', 'actor.actor_id')
            ->select(
                'inventory.inventory_id',
                'film.title',
                'film.release_year',
                'film.rating',
                'language.name as language',
                DB::raw('GROUP_CONCAT(DISTINCT category.name SEPARATOR ", ") as categories'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT(actor.first_name, " ", actor.last_name) SEPARATOR ", ") as actors'),
                'inventory.last_update'
            )
            ->where('inventory.store_id', $staff->store_id)
            ->groupBy('inventory.inventory_id', 'film.title', 'film.release_year', 'film.rating', 'language.name', 'inventory.last_update');

        // 🔍 Filtro por buscador
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('film.title', 'like', "%$search%")
                    ->orWhere('category.name', 'like', "%$search%")
                    ->orWhere('actor.first_name', 'like', "%$search%")
                    ->orWhere('actor.last_name', 'like', "%$search%")
                    ->orWhere('language.name', 'like', "%$search%");
            });
        }

        $peliculas = $query->orderBy('film.title')->get();

        return view('empleado.peliculas', compact('peliculas'));
    }

    public function marcarPelicula($id, Request $request)
    {
        DB::table('inventory')->where('inventory_id', $id)->update([
            'last_update' => now(),
        ]);

        return back()->with('status', 'La copia se marcó como ' . $request->status . ' correctamente.');
    }

    public function historialPelicula($id)
    {
        $historial = DB::table('rental')
            ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
            ->select('rental.rental_id', 'customer.first_name', 'customer.last_name', 'rental.rental_date', 'rental.return_date')
            ->where('rental.inventory_id', $id)
            ->orderBy('rental.rental_date', 'desc')
            ->get();

        return view('empleado.historial', compact('historial'));
    }

    // Vista de rentas
public function rentas()
{
    $rentas = DB::table('rental')
        ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
        ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
        ->join('film', 'inventory.film_id', '=', 'film.film_id')
        ->select(
            'rental.rental_id',
            DB::raw("CONCAT(customer.first_name, ' ', customer.last_name) AS cliente"),
            'film.title AS pelicula',
            'rental.rental_date',
            'rental.return_date',
            'film.rental_duration'
        )
        ->orderByDesc('rental.rental_date')
        ->get();

    $clientes = DB::table('customer')->get();

    $inventario = DB::table('inventory')
        ->join('film', 'inventory.film_id', '=', 'film.film_id')
        ->join('language', 'film.language_id', '=', 'language.language_id')
        ->select('inventory.inventory_id', 'film.title', 'language.name AS language')
        ->limit(200)
        ->get();

    return view('empleado.rentas', compact('rentas', 'clientes', 'inventario'));
}


// Guardar nueva renta
public function storeRenta(Request $request)
{
    $request->validate([
        'customer_id' => 'required',
        'inventory_id' => 'required'
    ]);

    // Validar que la copia esté disponible
    $disponible = DB::table('rental')
        ->where('inventory_id', $request->inventory_id)
        ->whereNull('return_date')
        ->doesntExist();

    if (!$disponible) {
        return back()->withErrors(['La película seleccionada no está disponible.']);
    }

    DB::table('rental')->insert([
        'rental_date' => now(),
        'inventory_id' => $request->inventory_id,
        'customer_id' => $request->customer_id,
        'staff_id' => 1,
        'last_update' => now(),
    ]);

    return back()->with('status', 'Renta registrada exitosamente.');
}
// Registrar devolución
public function devolver($id)
{
    DB::table('rental')
        ->where('rental_id', $id)
        ->update(['return_date' => now(), 'last_update' => now()]);

    return back()->with('status', 'Devolución registrada correctamente.');
}

    // Calcular cargos por retraso (para vista en tiempo real)
    public function calcularCargos()
    {
        $rentas = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
            ->select(
                'rental.rental_id',
                'film.title',
                'customer.first_name',
                'customer.last_name',
                DB::raw('DATEDIFF(NOW(), rental.rental_date + INTERVAL film.rental_duration DAY) AS dias_retraso')
            )
            ->whereNull('rental.return_date')
            ->having('dias_retraso', '>', 0)
            ->get();

        return response()->json($rentas);
    }

}
