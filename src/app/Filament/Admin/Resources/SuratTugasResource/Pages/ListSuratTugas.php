<?php

namespace App\Filament\Admin\Resources\SuratTugasResource\Pages;

use App\Filament\Admin\Resources\SuratTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratTugas extends ListRecords
{
    protected static string $resource = SuratTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
