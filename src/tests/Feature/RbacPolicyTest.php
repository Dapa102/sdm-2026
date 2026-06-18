<?php

use App\Models\AttendanceLog;
use App\Models\MeritTransaction;
use App\Models\RewardRequest;
use App\Models\SuratTugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows managers to approve attendance only for their subordinates', function () {
    $this->seed();

    $manager = User::where('email', 'manajer@sdm.test')->firstOrFail();
    $subordinate = User::where('email', 'karyawan@sdm.test')->firstOrFail();
    $otherEmployee = User::factory()->create();
    $otherEmployee->assignRole('karyawan');

    $subordinateSuratTugas = SuratTugas::create([
        'user_id' => $subordinate->id,
        'created_by' => $manager->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'location_name' => 'Kantor Cabang',
        'target_lat' => -6.2000000,
        'target_lng' => 106.8166660,
        'document_url' => 'surat-tugas/demo.pdf',
    ]);

    $otherSuratTugas = SuratTugas::create([
        'user_id' => $otherEmployee->id,
        'created_by' => $manager->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'location_name' => 'Kantor Lain',
        'target_lat' => -6.2000000,
        'target_lng' => 106.8166660,
        'document_url' => 'surat-tugas/other.pdf',
    ]);

    $subordinateLog = AttendanceLog::create([
        'surat_tugas_id' => $subordinateSuratTugas->id,
        'user_id' => $subordinate->id,
        'attendance_date' => now()->toDateString(),
    ]);

    $otherLog = AttendanceLog::create([
        'surat_tugas_id' => $otherSuratTugas->id,
        'user_id' => $otherEmployee->id,
        'attendance_date' => now()->toDateString(),
    ]);

    expect($manager->can('approve', $subordinateLog))->toBeTrue()
        ->and($manager->can('approve', $otherLog))->toBeFalse();
});

it('limits reward requests to owners and approvers', function () {
    $this->seed();

    $manager = User::where('email', 'manajer@sdm.test')->firstOrFail();
    $employee = User::where('email', 'karyawan@sdm.test')->firstOrFail();
    $otherEmployee = User::factory()->create();
    $otherEmployee->assignRole('karyawan');

    $request = RewardRequest::create([
        'user_id' => $employee->id,
        'reward_catalog_id' => \App\Models\RewardCatalog::firstOrFail()->id,
        'reason' => 'Penukaran point',
    ]);

    expect($employee->can('view', $request))->toBeTrue()
        ->and($manager->can('approve', $request))->toBeTrue()
        ->and($otherEmployee->can('view', $request))->toBeFalse();
});

it('keeps merit transactions immutable outside service flows', function () {
    $this->seed();

    $adminHr = User::where('email', 'admin.hr@sdm.test')->firstOrFail();
    $employee = User::where('email', 'karyawan@sdm.test')->firstOrFail();

    $transaction = MeritTransaction::create([
        'user_id' => $employee->id,
        'points' => 10,
        'type' => 'EARNED',
        'source' => 'TEST',
    ]);

    expect($employee->can('create', MeritTransaction::class))->toBeFalse()
        ->and($adminHr->can('update', $transaction))->toBeFalse()
        ->and($adminHr->can('delete', $transaction))->toBeFalse();
});
