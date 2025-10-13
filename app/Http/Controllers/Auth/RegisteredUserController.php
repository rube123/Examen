<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $stores = DB::table('store')->select('store_id')->orderBy('store_id')->get();
        $cities = DB::table('city')
            ->join('country','country.country_id','=','city.country_id')
            ->orderBy('country.country','asc')
            ->orderBy('city.city','asc')
            ->get(['city.city_id','city.city','country.country']);

        return view('auth.register', compact('stores','cities'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /*public function store(Request $request): RedirectResponse
>>>>>>> 6240fd4c090c320552a32d9a68f99d8f9dc67fd5
    {
        $request->validate([
            'name'         => ['required','string','max:255'],
            'email'        => ['required','string','email','max:255','unique:users,email'],
            'password'     => ['required','confirmed', Rules\Password::defaults()],
            'address'      => ['required','string','max:50'],
            'address2'     => ['nullable','string','max:50'],
            'district'     => ['required','string','max:20'],
            'city_id'      => ['required','integer','exists:city,city_id'],
            'postal_code'  => ['nullable','string','max:10'],
            'phone'        => ['required','string','max:20'],
            'store_id'     => ['required','integer','exists:store,store_id'],
        ]);

        $roleCustomerId = Role::where('name','customer')->value('id');

        $user = DB::transaction(function () use ($request, $roleCustomerId) {
            $addressId = DB::table('address')->insertGetId([
                'address'     => $request->input('address'),
                'address2'    => $request->input('address2'),
                'district'    => $request->input('district'),
                'city_id'     => (int)$request->input('city_id'),
                'postal_code' => $request->input('postal_code'),
                'phone'       => $request->input('phone'),
                'last_update' => now(),
            ]);

            $customerId = DB::table('customer')->insertGetId([
                'store_id'    => (int)$request->input('store_id'),
                'first_name'  => mb_substr($request->input('name'), 0, 45),
                'last_name'   => '-',
                'email'       => $request->input('email'),
                'address_id'  => $addressId,
                'active'      => 1,
                'create_date' => now(),
                'last_update' => now(),
            ]);

            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role_id'  => $roleCustomerId,
            ]);

           DB::table('customer_user')->insertOrIgnore([
    'user_id'     => $user->id,
    'customer_id' => $customerId,
]);
            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('customer.catalog');
=======
        return redirect(route('dashboard', absolute: false));
    }*/


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buscar el rol 'cliente'
        $clienteRole = Role::where('name', 'cliente')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $clienteRole ? $clienteRole->id : null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

}
