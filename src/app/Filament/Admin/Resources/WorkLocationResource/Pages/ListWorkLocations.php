<?php

namespace App\Filament\Admin\Resources\WorkLocationResource\Pages;

use App\Filament\Admin\Resources\WorkLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkLocations extends ListRecords
{
    protected static string $resource = WorkLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
