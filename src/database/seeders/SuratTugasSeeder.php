<?php

namespace Database\Seeders;

use App\Models\SuratTugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SuratTugasSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::query()->where('email', 'manajer@sdm.test')->firstOrFail();
        $employee = User::query()->where('email', 'karyawan@sdm.test')->firstOrFail();

        $documentPath = 'surat-tugas/demo-surat-tugas.pdf';

        Storage::disk('public')->put(
            $documentPath,
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF"
        );

        collect([
            [
                'location_name' => 'Kantor Cabang Jakarta Pusat',
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'target_lat' => -6.2000000,
                'target_lng' => 106.8166660,
                'radius_meters' => 300,
                'status' => 'ACTIVE',
            ],
            [
                'location_name' => 'Kantor Cabang Bandung',
                'start_date' => now()->addWeek()->toDateString(),
                'end_date' => now()->addWeek()->addDays(2)->toDateString(),
                'target_lat' => -6.9174640,
                'target_lng' => 107.6191230,
                'radius_meters' => 350,
                'status' => 'ACTIVE',
            ],
            [
                'location_name' => 'Kantor Cabang Bekasi',
                'start_date' => now()->subWeeks(2)->toDateString(),
                'end_date' => now()->subWeeks(2)->addDays(2)->toDateString(),
                'target_lat' => -6.2382700,
                'target_lng' => 106.9755730,
                'radius_meters' => 250,
                'status' => 'COMPLETED',
            ],
        ])->each(fn (array $suratTugas): SuratTugas => SuratTugas::updateOrCreate(
            [
                'user_id' => $employee->id,
                'location_name' => $suratTugas['location_name'],
                'start_date' => $suratTugas['start_date'],
            ],
            [
                ...$suratTugas,
                'user_id' => $employee->id,
                'created_by' => $manager->id,
                'document_url' => $documentPath,
            ]
        ));
    }
}
