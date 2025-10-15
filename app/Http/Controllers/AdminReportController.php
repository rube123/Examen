<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', now()->toDateString());
        $storeId = $request->input('store_id');

        // Filtro dinámico
        $rentas = DB::table('rental')
            ->when($storeId, function ($q) use ($storeId) {
                $q->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                  ->join('store', 'inventory.store_id', '=', 'store.store_id')
                  ->where('store.store_id', $storeId);
            })
            ->whereBetween('rental.rental_date', [$fechaInicio, $fechaFin])
            ->count();

        // Películas más rentadas
        $topPeliculas = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->when($storeId, fn($q) => $q->where('inventory.store_id', $storeId))
            ->whereBetween('rental.rental_date', [$fechaInicio, $fechaFin])
            ->select('film.title', DB::raw('COUNT(*) as total'))
            ->groupBy('film.title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Ingresos por tienda
        $ingresosPorTienda = DB::table('payment')
            ->join('staff', 'payment.staff_id', '=', 'staff.staff_id')
            ->join('store', 'staff.store_id', '=', 'store.store_id')
            ->whereBetween('payment.payment_date', [$fechaInicio, $fechaFin])
            ->select('store.store_id', DB::raw('SUM(payment.amount) as total_ingresos'))
            ->groupBy('store.store_id')
            ->get();

        // Clientes con más rentas
        $clientesTop = DB::table('rental')
            ->join('customer', 'rental.customer_id', '=', 'customer.customer_id')
            ->when($storeId, function ($q) use ($storeId) {
                $q->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                  ->where('inventory.store_id', $storeId);
            })
            ->whereBetween('rental.rental_date', [$fechaInicio, $fechaFin])
            ->select(DB::raw('CONCAT(customer.first_name, " ", customer.last_name) as cliente'),
                     DB::raw('COUNT(rental.rental_id) as total_rentas'))
            ->groupBy('cliente')
            ->orderByDesc('total_rentas')
            ->limit(10)
            ->get();

        // Tiendas disponibles
        $tiendas = DB::table('store')->select('store_id')->get();

        return view('admin.reportes', compact(
            'topPeliculas', 'ingresosPorTienda', 'clientesTop',
            'tiendas', 'fechaInicio', 'fechaFin', 'storeId'
        ));
    }

    // Exportar a CSV
    public function exportCsv()
    {
        $data = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->select('film.title', DB::raw('COUNT(rental.rental_id) as total'))
            ->groupBy('film.title')
            ->get();

        $csv = "Película,Total Rentas\n";
        foreach ($data as $row) {
            $csv .= "{$row->title},{$row->total}\n";
        }

        $filename = "reporte_rentas_" . now()->format('Ymd_His') . ".csv";
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=$filename");
    }

    // Exportar a PDF
    public function exportPdf()
    {
        $topPeliculas = DB::table('rental')
            ->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->select('film.title', DB::raw('COUNT(*) as total'))
            ->groupBy('film.title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $pdf = \PDF::loadView('admin.reportes_pdf', compact('topPeliculas'));
        return $pdf->download('reporte_peliculas.pdf');
    }
}
