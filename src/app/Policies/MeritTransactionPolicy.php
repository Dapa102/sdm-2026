<?php

namespace App\Policies;

use App\Models\MeritTransaction;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class MeritTransactionPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'merit::transaction';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, MeritTransaction $meritTransaction): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user)
            || $this->owns($user, $meritTransaction->user_id)
            || $this->managesUser($user, $meritTransaction->user_id));
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MeritTransaction $meritTransaction): bool
    {
        return false;
    }

    public function delete(User $user, MeritTransaction $meritTransaction): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
