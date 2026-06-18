<?php

use App\Models\AttendanceLog;
use App\Models\SuratTugas;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function testPhotoData(): string
{
    return 'data:image/jpeg;base64,' . base64_encode('fake-image-content');
}

function activeSuratTugasFor(User $user, array $overrides = []): SuratTugas
{
    return SuratTugas::create(array_merge([
        'user_id' => $user->id,
        'created_by' => null,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
        'location_name' => 'Kantor Cabang',
        'target_lat' => -6.2000000,
        'target_lng' => 106.8166660,
        'radius_meters' => 300,
        'document_url' => 'surat-tugas/demo.pdf',
        'status' => 'ACTIVE',
    ], $overrides));
}

it('stores a valid check-in inside the assignment radius', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $suratTugas = activeSuratTugasFor($user);

    $log = app(AttendanceService::class)->checkIn(
        $user,
        $suratTugas,
        -6.2000000,
        106.8166660,
        testPhotoData(),
        now(),
    );

    expect($log->location_status)->toBe('VALID')
        ->and($log->approval_status)->toBe('PENDING')
        ->and($log->check_in_photo_url)->not->toBeNull();

    Storage::disk('public')->assertExists($log->check_in_photo_url);
});

it('stores out of range check-ins as pending verification', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $suratTugas = activeSuratTugasFor($user);

    $log = app(AttendanceService::class)->checkIn(
        $user,
        $suratTugas,
        -6.3000000,
        106.9000000,
        testPhotoData(),
        now(),
    );

    expect($log->location_status)->toBe('OUT_OF_RANGE')
        ->and($log->approval_status)->toBe('PENDING');
});

it('allows check-in on the surat tugas end date', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $checkedInAt = now()->setTime(15, 0);
    $suratTugas = activeSuratTugasFor($user, [
        'start_date' => $checkedInAt->copy()->subDay()->toDateString(),
        'end_date' => $checkedInAt->toDateString(),
    ]);

    $log = app(AttendanceService::class)->checkIn(
        $user,
        $suratTugas,
        -6.2000000,
        106.8166660,
        testPhotoData(),
        $checkedInAt,
    );

    expect($log->attendance_date->isSameDay($checkedInAt))->toBeTrue();
});

it('prevents duplicate check-ins for the same assignment and date', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $suratTugas = activeSuratTugasFor($user);
    $service = app(AttendanceService::class);

    $service->checkIn($user, $suratTugas, -6.2000000, 106.8166660, testPhotoData(), now());

    expect(fn () => $service->checkIn($user, $suratTugas, -6.2000000, 106.8166660, testPhotoData(), now()))
        ->toThrow(ValidationException::class);
});

it('requires seven hours before check-out', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $suratTugas = activeSuratTugasFor($user);
    $service = app(AttendanceService::class);

    $checkInAt = now()->setTime(8, 0);
    $log = $service->checkIn($user, $suratTugas, -6.2000000, 106.8166660, testPhotoData(), $checkInAt);

    expect(fn () => $service->checkOut($user, $log, -6.2000000, 106.8166660, testPhotoData(), $checkInAt->copy()->addHours(6)))
        ->toThrow(ValidationException::class);

    $checkedOut = $service->checkOut(
        $user,
        $log,
        -6.2000000,
        106.8166660,
        testPhotoData(),
        $checkInAt->copy()->addHours(7),
    );

    expect($checkedOut->check_out_at)->not->toBeNull()
        ->and(AttendanceLog::count())->toBe(1);

    Storage::disk('public')->assertExists($checkedOut->check_out_photo_url);
});

it('requires check-out on the same attendance date', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $suratTugas = activeSuratTugasFor($user, [
        'end_date' => now()->addDays(2)->toDateString(),
    ]);
    $service = app(AttendanceService::class);

    $checkInAt = now()->setTime(16, 0);
    $log = $service->checkIn($user, $suratTugas, -6.2000000, 106.8166660, testPhotoData(), $checkInAt);

    expect(fn () => $service->checkOut(
        $user,
        $log,
        -6.2000000,
        106.8166660,
        testPhotoData(),
        $checkInAt->copy()->addDay()->setTime(8, 0),
    ))->toThrow(ValidationException::class);
});
