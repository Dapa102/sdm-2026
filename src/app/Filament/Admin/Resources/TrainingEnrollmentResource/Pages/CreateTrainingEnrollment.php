<?php

namespace App\Filament\Admin\Resources\TrainingEnrollmentResource\Pages;

use App\Filament\Admin\Resources\TrainingEnrollmentResource;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Services\MeritService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainingEnrollment extends CreateRecord
{
    protected static string $resource = TrainingEnrollmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'ENROLLED';

        return $data;
    }

    protected function beforeCreate(): void
    {
        $trainingId = $this->data['training_id'] ?? null;
        $userId = auth()->id();

        if (! $trainingId) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Pilih pelatihan terlebih dahulu.')
                ->send();

            $this->halt();
        }

        $training = Training::find($trainingId);

        if (! $training || ! $training->is_active) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Pelatihan tidak tersedia atau tidak aktif.')
                ->send();

            $this->halt();
        }

        $alreadyEnrolled = TrainingEnrollment::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->exists();

        if ($alreadyEnrolled) {
            Notification::make()
                ->danger()
                ->title('Sudah Terdaftar')
                ->body('Anda sudah terdaftar di pelatihan ini.')
                ->send();

            $this->halt();
        }

        $meritService = app(MeritService::class);
        $balance = $meritService->getBalance(auth()->user());

        if ($balance < $training->minimum_points) {
            Notification::make()
                ->danger()
                ->title('Point Tidak Cukup')
                ->body("Saldo point Anda {$balance} pt, dibutuhkan minimal {$training->minimum_points} pt.")
                ->send();

            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
