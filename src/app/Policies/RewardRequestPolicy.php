<?php

namespace App\Policies;

use App\Models\RewardRequest;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class RewardRequestPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'reward::request';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, RewardRequest $rewardRequest): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user)
            || $this->owns($user, $rewardRequest->user_id)
            || $this->managesUser($user, $rewardRequest->user_id));
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create_' . self::RESOURCE)
            && ($this->isEmployee($user) || $this->isManager($user));
    }

    public function update(User $user, RewardRequest $rewardRequest): bool
    {
        if (! $this->hasPermission($user, 'update_' . self::RESOURCE)) {
            return false;
        }

        if ($this->isHr($user) || $this->managesUser($user, $rewardRequest->user_id)) {
            return true;
        }

        return $this->owns($user, $rewardRequest->user_id) && $rewardRequest->status === 'PENDING';
    }

    public function approve(User $user, RewardRequest $rewardRequest): bool
    {
        return $this->hasPermission($user, 'approve_' . self::RESOURCE)
            && ($this->isHr($user) || $this->managesUser($user, $rewardRequest->user_id));
    }

    public function reject(User $user, RewardRequest $rewardRequest): bool
    {
        return $this->approve($user, $rewardRequest);
    }

    public function delete(User $user, RewardRequest $rewardRequest): bool
    {
        return $this->hasPermission($user, 'delete_' . self::RESOURCE)
            && $this->owns($user, $rewardRequest->user_id)
            && $rewardRequest->status === 'PENDING';
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
