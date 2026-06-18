<?php

namespace App\Filament\Admin\Widgets;

use App\Models\MeritTransaction;
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

        $totalPointsUsed = abs(MeritTransaction::where('type', 'DEBIT')->sum('points'));

        $totalPointsEarned = MeritTransaction::where('type', 'CREDIT')
            ->where('is_expired', false)
            ->sum('points');

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

            Stat::make('Point Ditukar', number_format($totalPointsUsed) . ' pt')
                ->description('Total point yang telah ditukar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),

            Stat::make('Point Beredar', number_format($totalPointsEarned) . ' pt')
                ->description('Total point aktif karyawan')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'admin_hr']);
    }
}
