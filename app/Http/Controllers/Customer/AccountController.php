<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    protected function customerIdForUser(int $userId): ?int
    {
        $row = DB::table('customer_user')->where('user_id',$userId)->first();
        return $row->customer_id ?? null;
    }

    // public function rentalsHistory(Request $request)
    // {
    //     $cid = $this->customerIdForUser($request->user()->id);
    //     if (!$cid) return response()->json(['message'=>'No vinculado a customer'], 404);

    //     $rows = DB::table('rental as r')
    //         ->join('inventory as i','i.inventory_id','=','r.inventory_id')
    //         ->join('film as f','f.film_id','=','i.film_id')
    //         ->select('r.rental_id','r.rental_date','r.return_date','i.store_id','f.film_id','f.title','f.rental_duration','f.rental_rate')
    //         ->where('r.customer_id',$cid)
    //         ->orderByDesc('r.rental_date')
    //         ->paginate(20);

    //     return response()->json($rows);
    // }

    // public function payments(Request $request)
    // {
    //     $cid = $this->customerIdForUser($request->user()->id);
    //     if (!$cid) return response()->json(['message'=>'No vinculado a customer'], 404);

    //     $rows = DB::table('payment as p')
    //         ->leftJoin('rental as r','r.rental_id','=','p.rental_id')
    //         ->leftJoin('inventory as i','i.inventory_id','=','r.inventory_id')
    //         ->leftJoin('film as f','f.film_id','=','i.film_id')
    //         ->select('p.payment_id','p.amount','p.payment_date','p.staff_id','p.rental_id','f.film_id','f.title')
    //         ->where('p.customer_id',$cid)
    //         ->orderByDesc('p.payment_date')
    //         ->paginate(20);

    //     return response()->json($rows);
    // }

    // public function pendingCharges(Request $request)
    // {
    //     $cid = $this->customerIdForUser($request->user()->id);
    //     if (!$cid) return response()->json(['message'=>'No vinculado a customer'], 404);

    //     $fee = (float) env('DAILY_LATE_FEE', 1.00);

    //     $rows = DB::table('rental as r')
    //         ->join('inventory as i','i.inventory_id','=','r.inventory_id')
    //         ->join('film as f','f.film_id','=','i.film_id')
    //         ->select(
    //             'r.rental_id','r.rental_date','i.store_id','f.film_id','f.title','f.rental_duration','f.rental_rate',
    //             DB::raw('DATEDIFF(NOW(), DATE_ADD(r.rental_date, INTERVAL f.rental_duration DAY)) as days_late')
    //         )
    //         ->where('r.customer_id',$cid)
    //         ->whereNull('r.return_date')
    //         ->having('days_late','>',0)
    //         ->orderByDesc('days_late')
    //         ->get()
    //         ->map(function ($row) use ($fee) {
    //             $days = (int) max(0, $row->days_late);
    //             $row->estimated_charge = round($days * $fee, 2);
    //             return $row;
    //         });

    //     return response()->json([
    //         'daily_fee' => $fee,
    //         'items' => $rows,
    //     ]);
    // }
    public function rentalsHistory(Request $request)
    {
        $user = $request->user();

        // Intento 1: mapear por email (sakila.customer.email)
        $customerId = DB::table('customer')->where('email', $user->email)->value('customer_id');

        // Si tu app usa tabla pivote customer_user, descomenta este bloque:
        // if (!$customerId) {
        //     $customerId = DB::table('customer_user')->where('user_id', $user->id)->value('customer_id');
        // }

        // Si no encontramos customer asociado => lista vacía (pero vista HTML)
        if (!$customerId) {
            $rentals = collect();
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $rentals, 0, 20, 1, ['path' => $request->url(), 'query' => $request->query()]
            );
            return view('customer.account.rentals', compact('paginator'));
        }

        // Query base
        $q = DB::table('rental as r')
            ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
            ->join('film as f', 'f.film_id', '=', 'i.film_id')
            ->join('store as s', 's.store_id', '=', 'i.store_id')
            ->where('r.customer_id', $customerId)
            ->orderByDesc('r.rental_date')
            ->select(
                'r.rental_id',
                'r.rental_date',
                'r.return_date',
                's.store_id',
                'f.film_id',
                'f.title',
                'f.rental_duration',
                'f.rental_rate'
            );

        // Paginación
        $paginator = $q->paginate(20)->withQueryString();

        // Pasar a la vista: la vista calculará due_date/estatus/recargos
        return view('customer.account.rentals', compact('paginator'));
    }

    public function payments(\Illuminate\Http\Request $request)
{
    $user = $request->user();

    // 1) Resolver el customer_id (ajusta si usas pivote customer_user)
    $customerId = \DB::table('customer')->where('email', $user->email)->value('customer_id');
    // Si usas tabla pivote, descomenta y ajusta:
    // if (!$customerId) {
    //     $customerId = \DB::table('customer_user')->where('user_id', $user->id)->value('customer_id');
    // }

    if (!$customerId) {
        // sin cliente vinculado => vista vacía pero HTML
        $empty = collect();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $empty, 0, 20, 1, ['path' => $request->url(), 'query' => $request->query()]
        );
        $totalPage = 0.0;
        $totalAll  = 0.0;
        return view('customer.account.payments', compact('paginator','totalPage','totalAll'));
    }

    // 2) Query de pagos del cliente
    $q = \DB::table('payment as p')
        ->join('rental as r', 'r.rental_id', '=', 'p.rental_id')
        ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
        ->join('film as f', 'f.film_id', '=', 'i.film_id')
        ->leftJoin('staff as st', 'st.staff_id', '=', 'p.staff_id')
        ->where('r.customer_id', $customerId)
        ->orderByDesc('p.payment_date')
        ->select(
            'p.payment_id',
            'p.amount',
            'p.payment_date',
            'p.staff_id',
            'p.rental_id',
            'f.film_id',
            'f.title',
            \DB::raw("CONCAT(COALESCE(st.first_name,''),' ',COALESCE(st.last_name,'')) as staff_name")
        );

    // 3) Paginación
    $paginator = $q->paginate(20)->withQueryString();

    // 4) Totales (página y global)
    $totalPage = $paginator->getCollection()->sum(fn($row) => (float)$row->amount);
    $totalAll  = (float) \DB::table('payment as p')
        ->join('rental as r', 'r.rental_id', '=', 'p.rental_id')
        ->where('r.customer_id', $customerId)
        ->sum('p.amount');

    return view('customer.account.payments', compact('paginator','totalPage','totalAll'));
}

public function pendingCharges(\Illuminate\Http\Request $request)
{
    $user = $request->user();

    // 1) Resolver customer_id (ajusta si usas pivote)
    $customerId = \DB::table('customer')->where('email', $user->email)->value('customer_id');
    // if (!$customerId) {
    //   $customerId = \DB::table('customer_user')->where('user_id', $user->id)->value('customer_id');
    // }

    // 2) Tarifa diaria (ENV tiene prioridad; si no, 1.00)
    $dailyFee = (float) ($request->query('daily_fee') ?? env('DAILY_LATE_FEE', 1.00));

    // Si no hay customer, regresa vista vacía
    if (!$customerId) {
        $items = collect();
        $totalDue = 0.0;
        return view('customer.account.charges', compact('items','dailyFee','totalDue'));
    }

    // 3) Traer rentas EN CURSO (no devueltas) del cliente
    $rows = \DB::table('rental as r')
        ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
        ->join('film as f', 'f.film_id', '=', 'i.film_id')
        ->join('store as s', 's.store_id', '=', 'i.store_id')
        ->where('r.customer_id', $customerId)
        ->whereNull('r.return_date')
        ->orderByDesc('r.rental_date')
        ->select(
            'r.rental_id',
            'r.rental_date',
            'r.return_date',
            's.store_id',
            'f.film_id',
            'f.title',
            'f.rental_duration'
        )
        ->get();

    // 4) Calcular atraso y cargo pendiente SOLO si ya venció
    $items = $rows->map(function ($r) use ($dailyFee) {
        $rentalAt = \Carbon\Carbon::parse($r->rental_date);
        $dueAt    = (clone $rentalAt)->addDays((int)$r->rental_duration);

        $lateDays = max(0, \Carbon\Carbon::now()->startOfDay()->diffInDays($dueAt->startOfDay(), false) * -1);
        $fee      = $lateDays * $dailyFee;

        return (object) [
            'rental_id'   => $r->rental_id,
            'film_id'     => $r->film_id,
            'title'       => $r->title,
            'store_id'    => $r->store_id,
            'rental_at'   => $rentalAt,
            'due_at'      => $dueAt,
            'late_days'   => $lateDays,
            'fee'         => $fee,
        ];
    })->filter(fn ($x) => $x->late_days > 0) // solo vencidas
      ->values();

    $totalDue = (float) $items->sum('fee');

    return view('customer.account.charges', compact('items','dailyFee','totalDue'));
}

}
