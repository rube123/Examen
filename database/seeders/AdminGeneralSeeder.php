<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminGeneralSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrador general']
        )->id;

        User::firstOrCreate(
            ['email' => 'admin@sakila.test'],
            [
                'name'     => 'Administrador General',
                'password' => Hash::make('Cambiar.123'),
                'role_id'  => $adminRoleId,
            ]
        );
    }
}
