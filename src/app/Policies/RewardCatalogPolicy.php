<?php

namespace App\Policies;

use App\Models\RewardCatalog;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class RewardCatalogPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'reward::catalog';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, RewardCatalog $rewardCatalog): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user) || $rewardCatalog->is_active);
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create_' . self::RESOURCE) && $this->isHr($user);
    }

    public function update(User $user, RewardCatalog $rewardCatalog): bool
    {
        return $this->hasPermission($user, 'update_' . self::RESOURCE) && $this->isHr($user);
    }

    public function delete(User $user, RewardCatalog $rewardCatalog): bool
    {
        return $this->hasPermission($user, 'delete_' . self::RESOURCE) && $this->isHr($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, 'delete_any_' . self::RESOURCE) && $this->isHr($user);
    }
}
