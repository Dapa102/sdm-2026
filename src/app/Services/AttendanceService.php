<?php

namespace App\Services;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\SuratTugas;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function checkIn(
        User $user,
        SuratTugas $suratTugas,
        ?float $latitude,
        ?float $longitude,
        string $photoData,
        ?CarbonInterface $checkedInAt = null,
        ?string $notes = null,
    ): AttendanceLog {
        $checkedInAt = $checkedInAt ? Carbon::instance($checkedInAt) : now();

        $this->assertSuratTugasIsUsableByUser($user, $suratTugas, $checkedInAt);

        if ($suratTugas->attendanceLogs()->whereDate('attendance_date', $checkedInAt->toDateString())->exists()) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-in untuk surat tugas ini pada tanggal tersebut sudah ada.',
            ]);
        }

        $distance = null;
        $verificationStatus = 'perlu_verifikasi';
        $locationStatus = 'UNKNOWN';

        if ($latitude !== null && $longitude !== null) {
            $distance = $this->distanceInMeters(
                (float) $suratTugas->target_lat,
                (float) $suratTugas->target_lng,
                $latitude,
                $longitude,
            );

            $insideRadius = $distance <= (int) $suratTugas->radius_meters;
            $verificationStatus = $insideRadius ? 'valid' : 'di_luar_lokasi';
            $locationStatus = $insideRadius ? 'VALID' : 'OUT_OF_RANGE';
        }

        $employee = Employee::query()
            ->where('user_id', $user->id)
            ->first();

        return AttendanceLog::create([
            'surat_tugas_id' => $suratTugas->id,
            'user_id' => $user->id,
            'employee_id' => $employee?->id,
            'attendance_date' => $checkedInAt->toDateString(),
            'check_in_at' => $checkedInAt,
            'check_in_lat' => $latitude,
            'check_in_lng' => $longitude,
            'check_in_distance_meters' => $distance !== null ? (int) round($distance) : null,
            'check_in_photo_url' => $this->storePhoto($photoData, 'check-in'),
            'location_status' => $locationStatus,
            'attendance_status' => 'hadir',
            'verification_status' => $verificationStatus,
            'approval_status' => 'PENDING',
            'notes' => $notes,
        ]);
    }

    public function checkOut(
        User $user,
        AttendanceLog $attendanceLog,
        ?float $latitude,
        ?float $longitude,
        string $photoData,
        ?CarbonInterface $checkedOutAt = null,
        ?string $notes = null,
    ): AttendanceLog {
        $checkedOutAt = $checkedOutAt ? Carbon::instance($checkedOutAt) : now();

        if ((string) $attendanceLog->user_id !== (string) $user->id) {
            throw ValidationException::withMessages([
                'attendance' => 'Anda tidak dapat check-out untuk absensi user lain.',
            ]);
        }

        if (! $attendanceLog->check_in_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-out hanya bisa dilakukan setelah check-in.',
            ]);
        }

        if ($attendanceLog->check_out_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-out untuk absensi ini sudah tercatat.',
            ]);
        }

        if (! $attendanceLog->attendance_date->isSameDay($checkedOutAt)) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-out harus dilakukan pada hari yang sama dengan check-in.',
            ]);
        }

        $distance = null;
        $verificationStatus = $attendanceLog->verification_status;
        $locationStatus = $attendanceLog->location_status;

        if ($latitude === null || $longitude === null) {
            $verificationStatus = $verificationStatus === 'valid' ? 'perlu_verifikasi' : $verificationStatus;
            $locationStatus = $locationStatus === 'VALID' ? 'UNKNOWN' : $locationStatus;
        } else {
            $suratTugas = $attendanceLog->suratTugas;
            $distance = $this->distanceInMeters(
                (float) $suratTugas->target_lat,
                (float) $suratTugas->target_lng,
                $latitude,
                $longitude,
            );

            if ($distance > (int) $suratTugas->radius_meters) {
                $locationStatus = 'OUT_OF_RANGE';
                $verificationStatus = 'di_luar_lokasi';
            }
        }

        $attendanceLog->update([
            'check_out_at' => $checkedOutAt,
            'check_out_lat' => $latitude,
            'check_out_lng' => $longitude,
            'check_out_distance_meters' => $distance !== null ? (int) round($distance) : null,
            'check_out_photo_url' => $this->storePhoto($photoData, 'check-out'),
            'location_status' => $locationStatus,
            'verification_status' => $verificationStatus,
            'notes' => $notes ?: $attendanceLog->notes,
        ]);

        return $attendanceLog->refresh();
    }

    public function distanceInMeters(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);
        $fromLat = deg2rad($fromLat);
        $toLat = deg2rad($toLat);

        $a = sin($latDelta / 2) ** 2
            + cos($fromLat) * cos($toLat) * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    public function correctAttendance(User $corrector, AttendanceLog $attendanceLog, array $changes, string $reason): AttendanceLog
    {
        $allowedFields = [
            'check_in_at',
            'check_out_at',
            'attendance_status',
            'verification_status',
            'notes',
            'check_in_lat',
            'check_in_lng',
            'check_out_lat',
            'check_out_lng',
        ];

        $updates = collect($changes)
            ->only($allowedFields)
            ->reject(fn (mixed $value): bool => $value === '')
            ->all();

        if ($updates === []) {
            throw ValidationException::withMessages([
                'correction' => 'Tidak ada data absensi yang dikoreksi.',
            ]);
        }

        $oldValue = collect(array_keys($updates))
            ->mapWithKeys(fn (string $field): array => [$field => $attendanceLog->getAttribute($field)])
            ->all();

        $updates['verification_status'] = 'dikoreksi_manual';

        $attendanceLog->update($updates);

        AttendanceCorrection::create([
            'attendance_log_id' => $attendanceLog->id,
            'corrected_by' => $corrector->id,
            'correction_type' => 'manual',
            'old_value' => $oldValue,
            'new_value' => $updates,
            'correction_reason' => $reason,
        ]);

        return $attendanceLog->refresh();
    }

    private function assertSuratTugasIsUsableByUser(User $user, SuratTugas $suratTugas, CarbonInterface $date): void
    {
        if ((string) $suratTugas->user_id !== (string) $user->id) {
            throw ValidationException::withMessages([
                'surat_tugas' => 'Surat tugas ini bukan milik Anda.',
            ]);
        }

        if ($suratTugas->status !== 'ACTIVE') {
            throw ValidationException::withMessages([
                'surat_tugas' => 'Surat tugas tidak aktif.',
            ]);
        }

        if ($suratTugas->start_date->toDateString() > $date->toDateString()
            || $suratTugas->end_date->toDateString() < $date->toDateString()) {
            throw ValidationException::withMessages([
                'surat_tugas' => 'Surat tugas tidak aktif untuk hari ini.',
            ]);
        }
    }

    private function storePhoto(string $photoData, string $type): string
    {
        if (! preg_match('/^data:image\/(?<extension>jpeg|jpg|png|webp);base64,(?<payload>.+)$/', $photoData, $matches)) {
            throw ValidationException::withMessages([
                'photo' => 'Foto tidak valid.',
            ]);
        }

        $binary = base64_decode($matches['payload'], true);

        if ($binary === false) {
            throw ValidationException::withMessages([
                'photo' => 'Foto tidak dapat dibaca.',
            ]);
        }

        $sizeInBytes = strlen($binary);
        $maxSizeInBytes = 5 * 1024 * 1024;

        if ($sizeInBytes > $maxSizeInBytes) {
            throw ValidationException::withMessages([
                'photo' => 'Ukuran foto terlalu besar. Maksimal 5MB.',
            ]);
        }

        $extension = $matches['extension'] === 'jpeg' ? 'jpg' : $matches['extension'];
        $path = sprintf('attendance/%s/%s/%s.%s', now()->format('Y/m'), $type, Str::uuid(), $extension);

        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
