<?php

namespace App\Services;

use App\Models\MeritTransaction;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\User;
use Illuminate\Support\Collection;

class TrainingRecommendationService
{
    public function __construct(
        private MeritService $meritService
    ) {}

    public function isEligibleForRecommendations(User $user): bool
    {
        $threeMonthsAgo = now()->subMonths(3);

        $totalCreditPoints = MeritTransaction::where('user_id', $user->id)
            ->where('type', 'CREDIT')
            ->where('created_at', '>=', $threeMonthsAgo)
            ->sum('points');

        return $totalCreditPoints >= 100;
    }

    public function getRecommendedTrainings(User $user): Collection
    {
        if (! $this->isEligibleForRecommendations($user)) {
            return collect([]);
        }

        return $this->getEligibleTrainings($user);
    }

    public function getEligibleTrainings(User $user): Collection
    {
        $currentBalance = $this->meritService->getBalance($user);

        $enrolledTrainingIds = TrainingEnrollment::where('user_id', $user->id)
            ->pluck('training_id')
            ->toArray();

        return Training::where('is_active', true)
            ->where('minimum_points', '<=', $currentBalance)
            ->whereNotIn('id', $enrolledTrainingIds)
            ->orderBy('minimum_points', 'asc')
            ->get();
    }

    public function getRecentEarnedPoints(User $user, int $months = 3): int
    {
        $startDate = now()->subMonths($months);

        return MeritTransaction::where('user_id', $user->id)
            ->where('type', 'CREDIT')
            ->where('created_at', '>=', $startDate)
            ->sum('points');
    }

    public function getAllActiveTrainings(): Collection
    {
        return Training::where('is_active', true)
            ->orderBy('minimum_points', 'asc')
            ->get();
    }

    public function canEnroll(User $user, Training $training): array
    {
        $currentBalance = $this->meritService->getBalance($user);
        $alreadyEnrolled = TrainingEnrollment::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->exists();

        return [
            'can_enroll' => $currentBalance >= $training->minimum_points 
                && ! $alreadyEnrolled 
                && $training->is_active,
            'has_sufficient_points' => $currentBalance >= $training->minimum_points,
            'already_enrolled' => $alreadyEnrolled,
            'is_active' => $training->is_active,
            'current_balance' => $currentBalance,
            'required_points' => $training->minimum_points,
        ];
    }
}
