<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Services\MeritService;
use App\Services\TrainingRecommendationService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AvailableTrainingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        $user = auth()->user();
        if (! $user) {
            return 'Pelatihan Tersedia';
        }

        $recommendationService = app(TrainingRecommendationService::class);
        $isEligible = $recommendationService->isEligibleForRecommendations($user);
        $recentPoints = $recommendationService->getRecentEarnedPoints($user, 3);

        if ($isEligible) {
            return "✨ Rekomendasi Pelatihan (Anda telah mengumpulkan {$recentPoints} pt dalam 3 bulan terakhir)";
        }

        return 'Pelatihan Tersedia (Kumpulkan 100 pt dalam 3 bulan untuk rekomendasi)';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $meritService = app(MeritService::class);
        $recommendationService = app(TrainingRecommendationService::class);

        $currentBalance = $user ? $meritService->getBalance($user) : 0;
        $isEligible = $user ? $recommendationService->isEligibleForRecommendations($user) : false;

        $enrolledTrainingIds = $user
            ? TrainingEnrollment::where('user_id', $user->id)->pluck('training_id')->toArray()
            : [];

        return $table
            ->query(
                Training::query()
                    ->where('is_active', true)
                    ->when($isEligible, function ($query) use ($currentBalance) {
                        return $query->where('minimum_points', '<=', $currentBalance);
                    })
                    ->whereNotIn('id', $enrolledTrainingIds)
                    ->orderBy('minimum_points', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Pelatihan')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('minimum_points')
                    ->label('Min. Point')
                    ->numeric()
                    ->suffix(' pt')
                    ->color(fn ($state): string => $state !== null && $currentBalance >= $state ? 'success' : 'danger'
                    )
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('duration_hours')
                    ->label('Durasi')
                    ->numeric()
                    ->suffix(' jam')
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('enroll')
                    ->label('Daftar')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->url(fn (): string => route('filament.admin.resources.training-enrollments.create'))
                    ->visible(fn (Training $record): bool => $currentBalance >= $record->minimum_points
                    ),
            ])
            ->emptyStateHeading('Tidak ada pelatihan tersedia')
            ->emptyStateDescription($isEligible
                ? 'Anda sudah terdaftar di semua pelatihan yang tersedia.'
                : 'Kumpulkan lebih banyak point atau tidak ada pelatihan aktif saat ini.'
            )
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->paginated(false);
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('karyawan');
    }
}
