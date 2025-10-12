<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersFromCustomersSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar que exista el rol 'customer'
        $customerRoleId = Role::where('name', 'customer')->value('id');
        if (!$customerRoleId) {
            $customerRoleId = Role::create([
                'name' => 'customer',
                'display_name' => 'Cliente',
            ])->id;
        }

        // Procesar en chunks para bases grandes
        DB::table('customer')->orderBy('customer_id')->chunk(500, function ($customers) use ($customerRoleId) {
            foreach ($customers as $c) {
                // Nombre y correo
                $fullName = trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: 'Customer '.$c->customer_id;

                $email = $c->email;
                if (!$email) {
                    // Generar uno si es NULL
                    $email = "customer{$c->customer_id}@sakila.local";
                }

                // Garantizar email único en users
                $email = $this->uniqueEmail($email);

                // ¿Ya existe un user con ese email?
                $user = User::where('email', $email)->first();

                if (!$user) {
                    $user = User::create([
                        'name'              => $fullName,
                        'email'             => $email,
                        'password'          => Hash::make('1234'),
                        'role_id'           => $customerRoleId,
                        'email_verified_at' => null,
                        'remember_token'    => Str::random(10),
                    ]);
                } else {
                    // Si existe, asegurar que tenga rol de customer (sin pisar otros cambios)
                    if ($user->role_id !== $customerRoleId) {
                        $user->role_id = $customerRoleId;
                        $user->save();
                    }
                }

                // Vincular en tabla puente (1:1) si no existe
                $exists = DB::table('customer_user')
                    ->where('user_id', $user->id)
                    ->orWhere('customer_id', $c->customer_id)
                    ->exists();

                if (!$exists) {
                    DB::table('customer_user')->insert([
                        'user_id'     => $user->id,
                        'customer_id' => $c->customer_id,
                    ]);
                }
            }
        });
    }

    /**
     * Si el email ya existe en users, genera variantes únicas.
     */
    private function uniqueEmail(string $email): string
    {
        $base = $email;
        $i = 1;

        // Si ya existe exactamente igual, vamos a generar variantes
        while (User::where('email', $email)->exists()) {
            if (str_contains($base, '@')) {
                [$local, $domain] = explode('@', $base, 2);
                $email = "{$local}+{$i}@{$domain}";
            } else {
                // Fallback raro (por si viniera sin @)
                $email = $base.$i.'@generated.local';
            }
            $i++;
        }

        return $email;
    }
}
