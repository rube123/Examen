<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',    'display_name' => 'Administrador general'],
            ['name' => 'employee', 'display_name' => 'Empleado de sucursal'],
            ['name' => 'customer', 'display_name' => 'Cliente'],
            ['name' => 'public',   'display_name' => 'Público general'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }
    }
}
