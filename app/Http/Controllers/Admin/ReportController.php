<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Reportes con filtros y 4 datasets paginados.
     * Filtros: store_id, desde (Y-m-d), hasta (Y-m-d), category_id.
     */
    public function index(Request $request)
    {
        $store_id    = $request->integer('store_id');
        $fecha_desde = $request->date('desde');
        $fecha_hasta = $request->date('hasta');
        $category_id = $request->integer('category_id');

        /* -------------------------------------------------------------
         * 1) RENTAS POR TIENDA (cuenta) + etiqueta de tienda (ciudad).
         *    r -> i -> store s -> address a -> city c
         * ----------------------------------------------------------- */
        $q_rentas = DB::table('rental as r')
            ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
            ->join('store as s', 's.store_id', '=', 'i.store_id')
            ->join('address as a', 'a.address_id', '=', 's.address_id')
            ->join('city as c', 'c.city_id', '=', 'a.city_id')
            ->select(
                'i.store_id',
                DB::raw("CONCAT('Store ', i.store_id, ' - ', c.city) AS store_label"),
                DB::raw('COUNT(*) AS total')
            )
            ->groupBy('i.store_id', 'c.city')
            ->orderByDesc('total');

        if ($store_id)    $q_rentas->where('i.store_id', $store_id);
        if ($fecha_desde) $q_rentas->whereDate('r.rental_date', '>=', $fecha_desde);
        if ($fecha_hasta) $q_rentas->whereDate('r.rental_date', '<=', $fecha_hasta);

        $rentas_por_tienda = $q_rentas
            ->paginate(20, ['*'], 'rentas_page')
            ->withQueryString(); // conserva filtros. :contentReference[oaicite:3]{index=3}

        /* -------------------------------------------------------------
         * 2) INGRESOS POR TIENDA (suma) + etiqueta de tienda (ciudad).
         *    p -> staff s -> store st -> address a -> city c
         * ----------------------------------------------------------- */
        $q_ingresos = DB::table('payment as p')
            ->join('staff as sf', 'sf.staff_id', '=', 'p.staff_id')
            ->join('store as st', 'st.store_id', '=', 'sf.store_id')
            ->join('address as a', 'a.address_id', '=', 'st.address_id')
            ->join('city as c', 'c.city_id', '=', 'a.city_id')
            ->select(
                'sf.store_id',
                DB::raw("CONCAT('Store ', sf.store_id, ' - ', c.city) AS store_label"),
                DB::raw('SUM(p.amount) AS total')
            )
            ->groupBy('sf.store_id', 'c.city')
            ->orderByDesc('total');

        if ($store_id)    $q_ingresos->where('sf.store_id', $store_id);
        if ($fecha_desde) $q_ingresos->whereDate('p.payment_date', '>=', $fecha_desde);
        if ($fecha_hasta) $q_ingresos->whereDate('p.payment_date', '<=', $fecha_hasta);

        $ingresos_por_tienda = $q_ingresos
            ->paginate(20, ['*'], 'ingresos_page')
            ->withQueryString();

        /* -------------------------------------------------------------
         * 3) PELÍCULAS MÁS RENTADAS (TOP) opcional por categoría.
         *    r -> i -> f  (+ fc/category si filtra)
         * ----------------------------------------------------------- */
        $q_top_films = DB::table('rental as r')
            ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
            ->join('film as f', 'f.film_id', '=', 'i.film_id')
            ->select('f.film_id', 'f.title', DB::raw('COUNT(*) AS veces'))
            ->groupBy('f.film_id', 'f.title')
            ->orderByDesc('veces');

        if ($store_id)    $q_top_films->where('i.store_id', $store_id);
        if ($fecha_desde) $q_top_films->whereDate('r.rental_date', '>=', $fecha_desde);
        if ($fecha_hasta) $q_top_films->whereDate('r.rental_date', '<=', $fecha_hasta);
        if ($category_id) {
            $q_top_films
                ->join('film_category as fc', 'fc.film_id', '=', 'f.film_id')
                ->where('fc.category_id', $category_id);
        }

        $peliculas_top = $q_top_films
            ->paginate(20, ['*'], 'peliculas_page')
            ->withQueryString();

        /* -------------------------------------------------------------
         * 4) CLIENTES CON MÁS RENTAS (TOP).
         *    r -> c
         * ----------------------------------------------------------- */
        $q_top_clientes = DB::table('rental as r')
            ->join('customer as c', 'c.customer_id', '=', 'r.customer_id')
            ->select(
                'c.customer_id',
                DB::raw("CONCAT(c.first_name, ' ', c.last_name) AS nombre"),
                'c.email',
                DB::raw('COUNT(*) AS veces')
            )
            ->groupBy('c.customer_id', 'c.first_name', 'c.last_name', 'c.email')
            ->orderByDesc('veces');

        if ($store_id)    $q_top_clientes->where('c.store_id', $store_id);
        if ($fecha_desde) $q_top_clientes->whereDate('r.rental_date', '>=', $fecha_desde);
        if ($fecha_hasta) $q_top_clientes->whereDate('r.rental_date', '<=', $fecha_hasta);

        $clientes_top = $q_top_clientes
            ->paginate(20, ['*'], 'clientes_page')
            ->withQueryString();

        // Catálogos para filtros.
        $tiendas    = DB::table('store')->select('store_id')->orderBy('store_id')->get();
        $categorias = DB::table('category')->select('category_id', 'name')->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'rentas_por_tienda',
            'ingresos_por_tienda',
            'peliculas_top',
            'clientes_top',
            'tiendas',
            'categorias'
        ));
    }

    /**
     * Exporta a CSV (streaming) con delimitador ';' y BOM UTF-8 para Excel.
     * type = rentas_tienda | ingresos_tienda | peliculas_top | clientes_top
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $tipo        = $request->string('type')->toString();
        $store_id    = $request->integer('store_id');
        $fecha_desde = $request->date('desde');
        $fecha_hasta = $request->date('hasta');
        $category_id = $request->integer('category_id');

        $nombre = match ($tipo) {
            'rentas_tienda'   => 'rentas_por_tienda',
            'ingresos_tienda' => 'ingresos_por_tienda',
            'peliculas_top'   => 'peliculas_mas_rentadas',
            'clientes_top'    => 'clientes_top',
            default           => 'reporte',
        };

        $filename = sprintf('%s_%s.csv', $nombre, now()->format('Ymd_His'));

        return response()->streamDownload(function () use ($tipo, $store_id, $fecha_desde, $fecha_hasta, $category_id) {
            $out = fopen('php://output', 'w');

            // ----- BOM UTF-8 para que Excel no malinterprete acentos/ñ.
            fwrite($out, "\xEF\xBB\xBF");

            // Usaremos ; como separador para Excel en regiones ES/LatAm.
            $sep = ';'; // fputcsv permite cambiar el delimitador. :contentReference[oaicite:4]{index=4}

            switch ($tipo) {
                case 'rentas_tienda':
                    // Cabeceras.
                    fputcsv($out, ['store_id', 'tienda', 'rentas'], $sep);

                    $q = DB::table('rental as r')
                        ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
                        ->join('store as s', 's.store_id', '=', 'i.store_id')
                        ->join('address as a', 'a.address_id', '=', 's.address_id')
                        ->join('city as c', 'c.city_id', '=', 'a.city_id')
                        ->select(
                            'i.store_id',
                            DB::raw("CONCAT('Store ', i.store_id, ' - ', c.city) AS store_label"),
                            DB::raw('COUNT(*) AS total')
                        )
                        ->groupBy('i.store_id', 'c.city')
                        ->orderByDesc('total');

                    if ($store_id)    $q->where('i.store_id', $store_id);
                    if ($fecha_desde) $q->whereDate('r.rental_date', '>=', $fecha_desde);
                    if ($fecha_hasta) $q->whereDate('r.rental_date', '<=', $fecha_hasta);

                    $q->chunk(1000, function ($rows) use ($out, $sep) {
                        foreach ($rows as $row) {
                            fputcsv($out, [$row->store_id, $row->store_label, $row->total], $sep);
                        }
                    });
                    break;

                case 'ingresos_tienda':
                    fputcsv($out, ['store_id', 'tienda', 'ingresos'], $sep);

                    $q = DB::table('payment as p')
                        ->join('staff as sf', 'sf.staff_id', '=', 'p.staff_id')
                        ->join('store as st', 'st.store_id', '=', 'sf.store_id')
                        ->join('address as a', 'a.address_id', '=', 'st.address_id')
                        ->join('city as c', 'c.city_id', '=', 'a.city_id')
                        ->select(
                            'sf.store_id',
                            DB::raw("CONCAT('Store ', sf.store_id, ' - ', c.city) AS store_label"),
                            DB::raw('SUM(p.amount) AS total')
                        )
                        ->groupBy('sf.store_id', 'c.city')
                        ->orderByDesc('total');

                    if ($store_id)    $q->where('sf.store_id', $store_id);
                    if ($fecha_desde) $q->whereDate('p.payment_date', '>=', $fecha_desde);
                    if ($fecha_hasta) $q->whereDate('p.payment_date', '<=', $fecha_hasta);

                    $q->chunk(1000, function ($rows) use ($out, $sep) {
                        foreach ($rows as $row) {
                            fputcsv($out, [$row->store_id, $row->store_label, number_format((float)$row->total, 2, '.', '')], $sep);
                        }
                    });
                    break;

                case 'peliculas_top':
                    fputcsv($out, ['film_id', 'title', 'veces'], $sep);

                    $q = DB::table('rental as r')
                        ->join('inventory as i', 'i.inventory_id', '=', 'r.inventory_id')
                        ->join('film as f', 'f.film_id', '=', 'i.film_id')
                        ->select('f.film_id', 'f.title', DB::raw('COUNT(*) AS veces'))
                        ->groupBy('f.film_id', 'f.title')
                        ->orderByDesc('veces');

                    if ($store_id)    $q->where('i.store_id', $store_id);
                    if ($fecha_desde) $q->whereDate('r.rental_date', '>=', $fecha_desde);
                    if ($fecha_hasta) $q->whereDate('r.rental_date', '<=', $fecha_hasta);
                    if ($category_id) {
                        $q->join('film_category as fc', 'fc.film_id', '=', 'f.film_id')
                          ->where('fc.category_id', $category_id);
                    }

                    $q->chunk(1000, function ($rows) use ($out, $sep) {
                        foreach ($rows as $row) {
                            fputcsv($out, [$row->film_id, $row->title, $row->veces], $sep);
                        }
                    });
                    break;

                case 'clientes_top':
                    fputcsv($out, ['customer_id', 'nombre', 'email', 'veces'], $sep);

                    $q = DB::table('rental as r')
                        ->join('customer as c', 'c.customer_id', '=', 'r.customer_id')
                        ->select(
                            'c.customer_id',
                            DB::raw("CONCAT(c.first_name, ' ', c.last_name) AS nombre"),
                            'c.email',
                            DB::raw('COUNT(*) AS veces')
                        )
                        ->groupBy('c.customer_id', 'c.first_name', 'c.last_name', 'c.email')
                        ->orderByDesc('veces');

                    if ($store_id)    $q->where('c.store_id', $store_id);
                    if ($fecha_desde) $q->whereDate('r.rental_date', '>=', $fecha_desde);
                    if ($fecha_hasta) $q->whereDate('r.rental_date', '<=', $fecha_hasta);

                    $q->chunk(1000, function ($rows) use ($out, $sep) {
                        foreach ($rows as $row) {
                            fputcsv($out, [$row->customer_id, $row->nombre, $row->email, $row->veces], $sep);
                        }
                    });
                    break;

                default:
                    fputcsv($out, ['mensaje'], $sep);
                    fputcsv($out, ['Tipo de reporte no reconocido. Usa type=rentas_tienda|ingresos_tienda|peliculas_top|clientes_top.'], $sep);
                    break;
            }

            fclose($out);
        }, $filename);
    }

    /**
     * Stub para PDF (pendiente de paquete).
     */
    public function exportPdf(Request $request)
    {
        return response('Exportar a PDF no está implementado todavía.', 501);
    }
}
