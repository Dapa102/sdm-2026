<?php

namespace App\Filament\Admin\Resources\MeritTransactionResource\Pages;

use App\Filament\Admin\Resources\MeritTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListMeritTransactions extends ListRecords
{
    protected static string $resource = MeritTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
