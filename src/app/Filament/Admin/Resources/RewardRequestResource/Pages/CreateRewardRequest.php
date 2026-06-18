<?php

namespace App\Filament\Admin\Resources\RewardRequestResource\Pages;

use App\Filament\Admin\Resources\RewardRequestResource;
use App\Models\RewardCatalog;
use App\Services\MeritService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRewardRequest extends CreateRecord
{
    protected static string $resource = RewardRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'PENDING';

        return $data;
    }

    protected function beforeCreate(): void
    {
        $rewardId = $this->data['reward_catalog_id'] ?? null;

        if (! $rewardId) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Pilih reward terlebih dahulu.')
                ->send();

            $this->halt();
        }

        $reward = RewardCatalog::find($rewardId);

        if (! $reward || ! $reward->is_active) {
            Notification::make()
                ->danger()
                ->title('Validasi Gagal')
                ->body('Reward tidak tersedia atau tidak aktif.')
                ->send();

            $this->halt();
        }

        $meritService = app(MeritService::class);
        $balance = $meritService->getBalance(auth()->user());

        if ($balance < $reward->point_cost) {
            Notification::make()
                ->danger()
                ->title('Saldo Tidak Cukup')
                ->body("Saldo point Anda {$balance} pt, dibutuhkan {$reward->point_cost} pt.")
                ->send();

            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
