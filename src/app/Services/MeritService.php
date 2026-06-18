<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\MeritTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MeritService
{
    public function addPoints(
        User $user,
        int $points,
        string $source,
        $reference = null,
        ?string $description = null
    ): MeritTransaction {
        return DB::transaction(function () use ($user, $points, $source, $reference, $description) {
            return MeritTransaction::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'CREDIT',
                'source' => $source,
                'reference_id' => $reference?->id,
                'reference_type' => $reference ? get_class($reference) : null,
                'description' => $description,
                'expiry_date' => now()->addMonths(12),
                'is_expired' => false,
            ]);
        });
    }

    public function deductPoints(
        User $user,
        int $points,
        string $source,
        $reference = null,
        ?string $description = null
    ): MeritTransaction {
        $balance = $this->getBalance($user);

        if ($balance < $points) {
            throw new \Exception('Insufficient points balance');
        }

        return DB::transaction(function () use ($user, $points, $source, $reference, $description) {
            return MeritTransaction::create([
                'user_id' => $user->id,
                'points' => -$points,
                'type' => 'DEBIT',
                'source' => $source,
                'reference_id' => $reference?->id,
                'reference_type' => $reference ? get_class($reference) : null,
                'description' => $description,
                'expiry_date' => null,
                'is_expired' => false,
            ]);
        });
    }

    public function getBalance(User $user): int
    {
        return MeritTransaction::where('user_id', $user->id)
            ->where('is_expired', false)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->sum('points');
    }

    public function processAttendanceApproval(AttendanceLog $attendanceLog): ?MeritTransaction
    {
        if ($attendanceLog->approval_status !== 'APPROVED') {
            return null;
        }

        $existingTransaction = MeritTransaction::where('reference_type', AttendanceLog::class)
            ->where('reference_id', $attendanceLog->id)
            ->where('source', 'attendance_approval')
            ->first();

        if ($existingTransaction) {
            return null;
        }

        return $this->addPoints(
            user: $attendanceLog->user,
            points: 10,
            source: 'attendance_approval',
            reference: $attendanceLog,
            description: 'Bonus absensi approved - ' . $attendanceLog->attendance_date->format('d M Y')
        );
    }

    public function processTrainingCompletion($trainingEnrollment): ?MeritTransaction
    {
        if ($trainingEnrollment->status !== 'COMPLETED') {
            return null;
        }

        $existingTransaction = MeritTransaction::where('reference_type', get_class($trainingEnrollment))
            ->where('reference_id', $trainingEnrollment->id)
            ->where('source', 'training_completion')
            ->first();

        if ($existingTransaction) {
            return null;
        }

        return $this->addPoints(
            user: $trainingEnrollment->user,
            points: 25,
            source: 'training_completion',
            reference: $trainingEnrollment,
            description: 'Bonus training completion'
        );
    }

    public function processRewardApproval($rewardRequest): ?MeritTransaction
    {
        if ($rewardRequest->approval_status !== 'APPROVED') {
            return null;
        }

        $existingTransaction = MeritTransaction::where('reference_type', get_class($rewardRequest))
            ->where('reference_id', $rewardRequest->id)
            ->where('source', 'reward_redemption')
            ->first();

        if ($existingTransaction) {
            return null;
        }

        return $this->deductPoints(
            user: $rewardRequest->user,
            points: $rewardRequest->rewardCatalog->points_required,
            source: 'reward_redemption',
            reference: $rewardRequest,
            description: 'Penukaran reward: ' . $rewardRequest->rewardCatalog->name
        );
    }
}
