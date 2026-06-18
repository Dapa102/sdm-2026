<?php

namespace App\Services;

use App\Models\AttendanceLog;
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
        float $latitude,
        float $longitude,
        string $photoData,
        ?CarbonInterface $checkedInAt = null,
    ): AttendanceLog {
        $checkedInAt = $checkedInAt ? Carbon::instance($checkedInAt) : now();

        $this->assertSuratTugasIsUsableByUser($user, $suratTugas, $checkedInAt);

        if ($suratTugas->attendanceLogs()->whereDate('attendance_date', $checkedInAt->toDateString())->exists()) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-in untuk surat tugas ini pada tanggal tersebut sudah ada.',
            ]);
        }

        $distance = $this->distanceInMeters(
            (float) $suratTugas->target_lat,
            (float) $suratTugas->target_lng,
            $latitude,
            $longitude,
        );

        return AttendanceLog::create([
            'surat_tugas_id' => $suratTugas->id,
            'user_id' => $user->id,
            'attendance_date' => $checkedInAt->toDateString(),
            'check_in_at' => $checkedInAt,
            'check_in_lat' => $latitude,
            'check_in_lng' => $longitude,
            'check_in_photo_url' => $this->storePhoto($photoData, 'check-in'),
            'location_status' => $distance <= (int) $suratTugas->radius_meters ? 'VALID' : 'OUT_OF_RANGE',
            'approval_status' => 'PENDING',
        ]);
    }

    public function checkOut(
        User $user,
        AttendanceLog $attendanceLog,
        float $latitude,
        float $longitude,
        string $photoData,
        ?CarbonInterface $checkedOutAt = null,
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

        if ($attendanceLog->check_in_at->copy()->addHours(7)->gt($checkedOutAt)) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-out hanya bisa dilakukan minimal 7 jam setelah check-in.',
            ]);
        }

        $attendanceLog->update([
            'check_out_at' => $checkedOutAt,
            'check_out_lat' => $latitude,
            'check_out_lng' => $longitude,
            'check_out_photo_url' => $this->storePhoto($photoData, 'check-out'),
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
