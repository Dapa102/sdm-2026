<?php

namespace App\Filament\Admin\Resources\RewardCatalogResource\Pages;

use App\Filament\Admin\Resources\RewardCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRewardCatalog extends EditRecord
{
    protected static string $resource = RewardCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
