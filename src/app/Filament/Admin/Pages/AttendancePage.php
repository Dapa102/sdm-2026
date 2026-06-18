<?php

namespace App\Filament\Admin\Pages;

use App\Models\AttendanceLog;
use App\Models\SuratTugas;
use App\Services\AttendanceService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;
use Throwable;

class AttendancePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'SDM';

    protected static ?string $navigationLabel = 'Absen Dinas Luar';

    protected static ?string $title = 'Absen Dinas Luar';

    protected static ?string $slug = 'absen-dinas-luar';

    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.admin.pages.attendance-page';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'admin_hr',
            'manajer',
            'karyawan',
        ]) ?? false;
    }

    public function getActiveSuratTugas(): Collection
    {
        return SuratTugas::query()
            ->with(['attendanceLogs' => fn ($query) => $query
                ->whereDate('attendance_date', now()->toDateString())
                ->latest()])
            ->where('user_id', auth()->id())
            ->where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    public function getRecentAttendanceLogs(): Collection
    {
        return AttendanceLog::query()
            ->with('suratTugas')
            ->where('user_id', auth()->id())
            ->latest('attendance_date')
            ->latest('check_in_at')
            ->limit(10)
            ->get();
    }

    /**
     * @return array<string, string>
     */
    public function getTodayStats(): array
    {
        $activeSuratTugas = $this->getActiveSuratTugas();
        $todayLogs = $activeSuratTugas
            ->flatMap(fn (SuratTugas $suratTugas): Collection => $suratTugas->attendanceLogs);

        return [
            'active_assignments' => Number::format($activeSuratTugas->count()),
            'checked_in' => Number::format($todayLogs->whereNotNull('check_in_at')->count()),
            'completed' => Number::format($todayLogs->whereNotNull('check_out_at')->count()),
            'pending_approval' => Number::format($todayLogs->where('approval_status', 'PENDING')->count()),
        ];
    }

    public function performCheckIn(string $suratTugasId, float $latitude, float $longitude, string $photoData): void
    {
        try {
            app(AttendanceService::class)->checkIn(
                auth()->user(),
                SuratTugas::query()->findOrFail($suratTugasId),
                $latitude,
                $longitude,
                $photoData,
            );

            Notification::make()
                ->success()
                ->title('Check-in tersimpan')
                ->send();
        } catch (ValidationException $exception) {
            $this->notifyValidationError($exception);
        } catch (Throwable) {
            Notification::make()
                ->danger()
                ->title('Check-in gagal')
                ->body('Silakan coba lagi.')
                ->send();
        }
    }

    public function performCheckOut(string $attendanceLogId, float $latitude, float $longitude, string $photoData): void
    {
        try {
            app(AttendanceService::class)->checkOut(
                auth()->user(),
                AttendanceLog::query()->findOrFail($attendanceLogId),
                $latitude,
                $longitude,
                $photoData,
            );

            Notification::make()
                ->success()
                ->title('Check-out tersimpan')
                ->send();
        } catch (ValidationException $exception) {
            $this->notifyValidationError($exception);
        } catch (Throwable) {
            Notification::make()
                ->danger()
                ->title('Check-out gagal')
                ->body('Silakan coba lagi.')
                ->send();
        }
    }

    private function notifyValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title(collect($exception->errors())->flatten()->first() ?? 'Data absensi tidak valid')
            ->send();
    }
}
