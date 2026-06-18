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
        $manager = User::updateOrCreate(
            ['email' => 'manajer@sdm.test'],
            ['name' => 'Manajer', 'password' => Hash::make('password')]
        );
        $manager->syncRoles(['manajer']);

        collect([
            ['name' => 'Super Admin', 'email' => 'admin@admin.com', 'role' => 'super_admin', 'manager_id' => null],
            ['name' => 'Admin HR', 'email' => 'admin.hr@sdm.test', 'role' => 'admin_hr', 'manager_id' => null],
            ['name' => 'Karyawan', 'email' => 'karyawan@sdm.test', 'role' => 'karyawan', 'manager_id' => $manager->id],
        ])->each(function (array $userData): void {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'manager_id' => $userData['manager_id'],
                ]
            );

            $user->syncRoles([$userData['role']]);
        });
    }
}
