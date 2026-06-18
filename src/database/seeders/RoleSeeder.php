<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'super_admin',
            'admin_hr',
            'manajer',
            'karyawan',
        ])->each(fn (string $role) => Role::firstOrCreate(['name' => $role]));
    }
}
