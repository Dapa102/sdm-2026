<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Super Admin', 'email' => 'admin@admin.com', 'role' => 'super_admin'],
            ['name' => 'Admin HR', 'email' => 'admin.hr@sdm.test', 'role' => 'admin_hr'],
            ['name' => 'Manajer', 'email' => 'manajer@sdm.test', 'role' => 'manajer'],
            ['name' => 'Karyawan', 'email' => 'karyawan@sdm.test', 'role' => 'karyawan'],
        ])->each(function (array $userData): void {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => Hash::make('password')]
            );

            $user->syncRoles([$userData['role']]);
        });
    }
}
