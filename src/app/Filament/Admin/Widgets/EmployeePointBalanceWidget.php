<?php

namespace App\Filament\Admin\Widgets;

use App\Models\MeritTransaction;
use App\Services\MeritService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeePointBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || ! $user->hasRole('karyawan')) {
            return [];
        }

        $meritService = app(MeritService::class);
        $currentBalance = $meritService->getBalance($user);

        $totalEarned = MeritTransaction::where('user_id', $user->id)
            ->where('type', 'CREDIT')
            ->where('is_expired', false)
            ->sum('points');

        $totalSpent = abs(MeritTransaction::where('user_id', $user->id)
            ->where('type', 'DEBIT')
            ->sum('points'));

        $expiringSoon = MeritTransaction::where('user_id', $user->id)
            ->where('type', 'CREDIT')
            ->where('is_expired', false)
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->sum('points');

        return [
            Stat::make('Saldo Point Aktif', $currentBalance . ' pt')
                ->description('Total point yang dapat digunakan')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->url(route('filament.admin.resources.merit-transactions.index')),

            Stat::make('Point Diperoleh', $totalEarned . ' pt')
                ->description('Total point yang didapat')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Point Digunakan', $totalSpent . ' pt')
                ->description('Total point yang telah ditukar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),

            Stat::make('Akan Kadaluarsa', $expiringSoon . ' pt')
                ->description('Kadaluarsa dalam 30 hari')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'gray'),
        ];
    }

    public static function canView(): bool
    {
        return false;
    }
}
