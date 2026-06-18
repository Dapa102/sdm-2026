<?php

namespace App\Filament\Admin\Widgets;

use App\Models\SuratTugas;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveAssignmentWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || ! $user->hasRole('karyawan')) {
            return [];
        }

        $activeST = SuratTugas::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();

        $todayAttendance = \App\Models\AttendanceLog::where('user_id', $user->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->count();

        return [
            Stat::make('Surat Tugas Aktif', $activeST)
                ->description('Surat tugas aktif hari ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($activeST > 0 ? 'success' : 'gray')
                ->url(route('filament.admin.pages.absen-dinas-luar')),

            Stat::make('Absensi Hari Ini', $todayAttendance)
                ->description($todayAttendance > 0 ? 'Check-in tercatat' : 'Belum check-in')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($todayAttendance > 0 ? 'success' : 'warning')
                ->url(route('filament.admin.pages.absen-dinas-luar')),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('karyawan');
    }
}
