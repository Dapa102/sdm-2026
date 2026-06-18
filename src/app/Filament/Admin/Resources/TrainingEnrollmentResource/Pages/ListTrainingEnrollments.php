<?php

namespace App\Filament\Admin\Resources\TrainingEnrollmentResource\Pages;

use App\Filament\Admin\Resources\TrainingEnrollmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingEnrollments extends ListRecords
{
    protected static string $resource = TrainingEnrollmentResource::class;

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
