<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FilmController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $filmes = Film::query()
            ->when($q, fn($x)=>$x->where('title','like',"%$q%"))
            ->orderBy('film_id','desc')
            ->paginate(20)->withQueryString();

        return view('admin.films.index', compact('filmes','q'));
    }

    public function create()
    {
        $idiomas    = DB::connection('sakila')->table('language')->get();
        $categorias = Category::orderBy('name')->get();
        return view('admin.films.create', compact('idiomas','categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'title'             => ['required','string','max:255'],
            'description'       => ['nullable','string'],
            'release_year'      => ['nullable','integer'],
            'language_id'       => ['required','integer','exists:sakila.language,language_id'],
            'rental_duration'   => ['required','integer'],
            'rental_rate'       => ['required','numeric'],
            'length'            => ['nullable','integer'],
            'replacement_cost'  => ['required','numeric'],
            'rating'            => ['nullable','string','max:10'],
            'category_ids'      => ['array'],
        ]);

        $film = Film::create($datos);

        // Vincular categorías si se pasan (tabla film_category)
        if (!empty($datos['category_ids'])) {
            foreach ($datos['category_ids'] as $cat) {
                DB::connection('sakila')->table('film_category')->insert([
                    'film_id' => $film->film_id,
                    'category_id' => (int)$cat,
                ]);
            }
        }

        return redirect()->route('admin.films.index')->with('ok','Película creada.');
    }

    public function edit(int $id)
    {
        $film       = Film::findOrFail($id);
        $idiomas    = DB::connection('sakila')->table('language')->get();
        $categorias = Category::orderBy('name')->get();
        $cats_sel   = DB::connection('sakila')->table('film_category')
                        ->where('film_id',$id)->pluck('category_id')->all();

        return view('admin.films.edit', compact('film','idiomas','categorias','cats_sel'));
    }

    public function update(Request $request, int $id)
    {
        $film = Film::findOrFail($id);

        $datos = $request->validate([
            'title'            => ['required','string','max:255'],
            'description'      => ['nullable','string'],
            'release_year'     => ['nullable','integer'],
            'language_id'      => ['required','integer','exists:sakila.language,language_id'],
            'rental_duration'  => ['required','integer'],
            'rental_rate'      => ['required','numeric'],
            'length'           => ['nullable','integer'],
            'replacement_cost' => ['required','numeric'],
            'rating'           => ['nullable','string','max:10'],
            'category_ids'     => ['array'],
        ]);

        $film->update($datos);

        // Actualizar categorías
        DB::connection('sakila')->table('film_category')->where('film_id',$film->film_id)->delete();
        foreach ($datos['category_ids'] ?? [] as $cat) {
            DB::connection('sakila')->table('film_category')->insert([
                'film_id'=>$film->film_id,'category_id'=>(int)$cat
            ]);
        }

        return back()->with('ok','Película actualizada.');
    }

    public function destroy(int $id)
    {
        // Para Sakila lo normal es no borrar films con inventario.
        DB::connection('sakila')->table('film_category')->where('film_id',$id)->delete();
        Film::where('film_id',$id)->delete();
        return back()->with('ok','Película eliminada.');
    }

    // ----- Importación desde OMDb -----
    public function importOmdb(Request $request)
    {
        $datos = $request->validate([
            'title' => ['required','string'],
            'year'  => ['nullable','integer'],
            'language_id' => ['required','integer','exists:sakila.language,language_id'],
            'category_id' => ['nullable','integer','exists:sakila.category,category_id'],
        ]);

        $apiKey = config('services.omdb.key') ?? env('OMDB_API_KEY');
        $resp = Http::get('https://www.omdbapi.com/', [
            't'       => $datos['title'],
            'y'       => $datos['year'],
            'apikey'  => $apiKey,
            'plot'    => 'short',
        ]); // Laravel HTTP client. :contentReference[oaicite:10]{index=10}

        if (!$resp->ok() || $resp->json('Response') === 'False') {
            return back()->with('error', 'No se encontró en OMDb: '.$resp->json('Error'));
        }

        $d = $resp->json(); // Campos OMDb: Title, Year, Rated, Plot, Runtime, etc. :contentReference[oaicite:11]{index=11}

        $film = Film::create([
            'title'            => $d['Title'] ?? $datos['title'],
            'description'      => $d['Plot'] ?? null,
            'release_year'     => isset($d['Year']) ? (int)substr($d['Year'],0,4) : null,
            'language_id'      => $datos['language_id'],
            'rental_duration'  => 3,
            'rental_rate'      => 2.99,
            'length'           => isset($d['Runtime']) ? (int)filter_var($d['Runtime'], FILTER_SANITIZE_NUMBER_INT) : null,
            'replacement_cost' => 19.99,
            'rating'           => $d['Rated'] ?? null,
            'special_features' => null,
        ]);

        if (!empty($datos['category_id'])) {
            DB::connection('sakila')->table('film_category')->insert([
                'film_id' => $film->film_id,
                'category_id' => (int)$datos['category_id'],
            ]);
        }

        return redirect()->route('admin.films.edit', $film->film_id)
            ->with('ok','Importado desde OMDb.');
    }
}
