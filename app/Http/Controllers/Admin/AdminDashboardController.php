<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirecciones por rol (hazlo del lado servidor, no con JS en Blade).
        if ($user?->isRole('employee')) {
            return redirect()->route('empleado.dashboard');
        }
        if ($user?->isRole('customer')) {
            return redirect()->route('customer.catalog');
        }

        // KPIs para el dashboard de Admin.
        return view('admin.dashboard', [
            'stores'       => \App\Models\Store::count(),
            'employees'    => \App\Models\Staff::count(),
            'films'        => \App\Models\Film::count(),
            'inventory'    => \App\Models\Inventory::count(),
            'rentalsToday' => \App\Models\Rental::whereDate('rental_date', now()->toDateString())->count(),
        ]);
    }
}
