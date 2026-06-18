<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RewardRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingRewardRequestWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $query = RewardRequest::where('status', 'PENDING');

        if ($user->hasRole('manajer') && ! $user->hasAnyRole(['super_admin', 'admin_hr'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        }

        $pendingCount = $query->count();

        return [
            Stat::make('Pending Reward Request', $pendingCount)
                ->description('Penukaran reward menunggu persetujuan')
                ->descriptionIcon('heroicon-m-gift')
                ->color($pendingCount > 0 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.reward-requests.index', [
                    'tableFilters' => [
                        'status' => ['value' => 'PENDING'],
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
