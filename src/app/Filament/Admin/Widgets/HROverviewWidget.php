<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AttendanceLog;
use App\Models\SuratTugas;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HROverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || ! $user->hasAnyRole(['super_admin', 'admin_hr'])) {
            return [];
        }

        $totalEmployees = User::whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))->count();

        $activeST = SuratTugas::where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();

        $todayAttendance = AttendanceLog::whereDate('attendance_date', now()->toDateString());
        $presentToday = (clone $todayAttendance)->where('attendance_status', 'hadir')->count();
        $lateToday = (clone $todayAttendance)->where('attendance_status', 'terlambat')->count();
        $needsVerification = (clone $todayAttendance)
            ->whereIn('verification_status', ['perlu_verifikasi', 'di_luar_lokasi'])
            ->count();

        return [
            Stat::make('Total Karyawan', $totalEmployees)
                ->description('Karyawan terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->url(route('filament.admin.resources.users.index')),

            Stat::make('Surat Tugas Aktif', $activeST)
                ->description('ST aktif saat ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->url(route('filament.admin.resources.surat-tugas.index', [
                    'tableFilters' => [
                        'status' => ['value' => 'ACTIVE'],
                    ],
                ])),

            Stat::make('Hadir Hari Ini', $presentToday)
                ->description("{$lateToday} terlambat")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.attendance-logs.index')),

            Stat::make('Perlu Verifikasi', $needsVerification)
                ->description('GPS gagal atau luar radius')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color($needsVerification > 0 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.attendance-logs.index')),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'admin_hr']);
    }
}
