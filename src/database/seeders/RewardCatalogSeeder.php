<?php

namespace Database\Seeders;

use App\Models\RewardCatalog;
use Illuminate\Database\Seeder;

class RewardCatalogSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'Voucher Belanja', 'point_cost' => 50],
            ['name' => 'Bonus Tunai', 'point_cost' => 100],
            ['name' => 'Cuti Tambahan 1 Hari', 'point_cost' => 75],
            ['name' => 'Merchandise', 'point_cost' => 25],
            ['name' => 'Training Premium', 'point_cost' => 200],
        ])->each(fn (array $reward) => RewardCatalog::updateOrCreate(
            ['name' => $reward['name']],
            ['point_cost' => $reward['point_cost'], 'is_active' => true]
        ));
    }
}
