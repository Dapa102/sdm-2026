<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeamMembersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || ! $user->hasRole('manajer')) {
            return [];
        }

        $teamCount = User::where('manager_id', $user->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'karyawan'))
            ->count();

        $activeToday = User::where('manager_id', $user->id)
            ->whereHas('attendanceLogs', function ($q) {
                $q->whereDate('attendance_date', now()->toDateString());
            })
            ->count();

        return [
            Stat::make('Tim Saya', $teamCount)
                ->description('Total anggota tim')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->url(route('filament.admin.resources.users.index', [
                    'tableFilters' => [
                        'manager_id' => ['value' => $user->id],
                    ],
                ])),

            Stat::make('Aktif Hari Ini', $activeToday)
                ->description('Anggota tim yang absen hari ini')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($activeToday > 0 ? 'success' : 'gray'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('manajer');
    }
}
