<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AttendanceLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingAttendanceApprovalWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $query = AttendanceLog::where('approval_status', 'PENDING');

        if ($user->hasRole('manajer') && ! $user->hasAnyRole(['super_admin', 'admin_hr'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        $pendingCount = $query->count();
        $outOfRangeCount = AttendanceLog::where('approval_status', 'PENDING')
            ->where('location_status', 'OUT_OF_RANGE');

        if ($user->hasRole('manajer') && ! $user->hasAnyRole(['super_admin', 'admin_hr'])) {
            $outOfRangeCount->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        $outOfRangeCount = $outOfRangeCount->count();

        return [
            Stat::make('Pending Approval', $pendingCount)
                ->description('Absensi menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.attendance-logs.index', [
                    'tableFilters' => [
                        'approval_status' => ['value' => 'PENDING'],
                    ],
                ])),

            Stat::make('Luar Radius', $outOfRangeCount)
                ->description('Absensi di luar radius')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('danger')
                ->url(route('filament.admin.resources.attendance-logs.index', [
                    'tableFilters' => [
                        'approval_status' => ['value' => 'PENDING'],
                        'location_status' => ['value' => 'OUT_OF_RANGE'],
                    ],
                ])),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'admin_hr', 'manajer']);
    }
}
