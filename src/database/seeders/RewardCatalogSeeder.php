<?php

namespace Database\Seeders;

use App\Models\RewardCatalog;
use Illuminate\Database\Seeder;

class RewardCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'name' => 'Voucher Belanja',
                'description' => 'Voucher belanja senilai Rp 500.000 yang dapat digunakan di berbagai merchant.',
                'point_cost' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Bonus Tunai',
                'description' => 'Bonus tunai Rp 1.000.000 yang akan ditransfer langsung ke rekening.',
                'point_cost' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Cuti Tambahan 1 Hari',
                'description' => 'Cuti tambahan 1 hari yang dapat digunakan diluar jatah cuti tahunan.',
                'point_cost' => 75,
                'is_active' => true,
            ],
            [
                'name' => 'Merchandise',
                'description' => 'Merchandise eksklusif perusahaan (tas, tumbler, jaket, dll).',
                'point_cost' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Training Premium',
                'description' => 'Akses training premium eksternal dengan sertifikat nasional/internasional.',
                'point_cost' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($rewards as $reward) {
            RewardCatalog::firstOrCreate(
                ['name' => $reward['name']],
                $reward
            );
        }

        $this->command->info('✓ Reward catalog seeded successfully.');
    }
}
