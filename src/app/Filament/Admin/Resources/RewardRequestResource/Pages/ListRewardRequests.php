<?php

namespace App\Filament\Admin\Resources\RewardRequestResource\Pages;

use App\Filament\Admin\Resources\RewardRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRewardRequests extends ListRecords
{
    protected static string $resource = RewardRequestResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('karyawan')) {
            return [
                Actions\CreateAction::make(),
            ];
        }

        return [];
    }
}
