<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * GET /customer/catalog
     * Filtros: title, store_id, category_id, language_id, available_only
     */
    public function index(Request $request)
    {
        // datos para selects
        $stores     = DB::table('store')->select('store_id')->orderBy('store_id')->get();
        $languages  = DB::table('language')->select('language_id','name')->orderBy('name')->get();
        $categories = DB::table('category')->select('category_id','name')->orderBy('name')->get();

        // filtros
        $title         = trim((string) $request->query('title', ''));
        $storeId       = (int) $request->query('store_id', 0);
        $categoryId    = (int) $request->query('category_id', 0);
        $languageId    = (int) $request->query('language_id', 0);
        $availableOnly = (bool) $request->boolean('available_only', false);

        // query base
        $q = DB::table('film as f')
            ->join('language as l','l.language_id','=','f.language_id')
            ->select(
                'f.film_id',
                'f.title',
                'f.description',
                'f.length',
                'f.rental_rate',
                'f.rental_duration',
                'l.name as language'
            );

        if ($title !== '') {
            $q->where('f.title','like',"%{$title}%");
        }
        if ($languageId) {
            $q->where('f.language_id',$languageId);
        }
        if ($categoryId) {
            $q->join('film_category as fc','fc.film_id','=','f.film_id')
              ->where('fc.category_id',$categoryId);
        }

        // disponibilidad por tienda (opcional)
        if ($storeId) {
            $availableSub = DB::table('inventory as i')
                ->leftJoin('rental as r', function ($j) {
                    $j->on('r.inventory_id','=','i.inventory_id')
                      ->whereNull('r.return_date');
                })
                ->where('i.store_id',$storeId)
                ->select('i.film_id', DB::raw('count(*) as available_count'))
                ->groupBy('i.film_id');

            $q->leftJoinSub($availableSub,'a','a.film_id','=','f.film_id')
              ->addSelect(DB::raw('COALESCE(a.available_count,0) as available_now'));

            if ($availableOnly) {
                $q->whereRaw('COALESCE(a.available_count,0) > 0');
            }
        }

        $films = $q->distinct()->orderBy('f.title')->paginate(12)->withQueryString();

        return view('customer.catalog.index', compact(
            'films','stores','languages','categories',
            'title','storeId','categoryId','languageId','availableOnly'
        ));
    }

    /**
     * GET /customer/films/{filmId}
     * Detalle con actores, categorías y disponibilidad por sucursal
     */
    public function show(int $filmId)
    {
        $film = DB::table('film as f')
            ->join('language as l','l.language_id','=','f.language_id')
            ->where('f.film_id',$filmId)
            ->select(
                'f.*',
                'l.name as language'
            )->first();

        abort_if(!$film, 404);

        $actors = DB::table('actor as a')
            ->join('film_actor as fa','fa.actor_id','=','a.actor_id')
            ->where('fa.film_id',$filmId)
            ->orderBy('a.last_name')->get(['a.first_name','a.last_name']);

        $categories = DB::table('category as c')
            ->join('film_category as fc','fc.category_id','=','c.category_id')
            ->where('fc.film_id',$filmId)
            ->orderBy('c.name')->get(['c.name']);

        // disponibilidad por sucursal (store)
        $availability = DB::table('store as s')
            ->leftJoin('inventory as i', function ($j) use ($filmId) {
                $j->on('i.store_id','=','s.store_id')->where('i.film_id',$filmId);
            })
            ->leftJoin('rental as r', function ($j) {
                $j->on('r.inventory_id','=','i.inventory_id')
                  ->whereNull('r.return_date'); // rentado actualmente
            })
            ->groupBy('s.store_id')
            ->orderBy('s.store_id')
            ->select('s.store_id', DB::raw('COUNT(i.inventory_id) - COUNT(r.rental_id) as available_now'))
            ->get();

        return view('customer.films.show', compact('film','actors','categories','availability'));
    }
}
