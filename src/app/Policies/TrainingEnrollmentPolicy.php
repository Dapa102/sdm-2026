<?php

namespace App\Policies;

use App\Models\TrainingEnrollment;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class TrainingEnrollmentPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'training::enrollment';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, TrainingEnrollment $trainingEnrollment): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user)
            || $this->owns($user, $trainingEnrollment->user_id)
            || $this->managesUser($user, $trainingEnrollment->user_id));
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create_' . self::RESOURCE)
            && ($this->isEmployee($user) || $this->isManager($user));
    }

    public function update(User $user, TrainingEnrollment $trainingEnrollment): bool
    {
        return $this->hasPermission($user, 'update_' . self::RESOURCE) && $this->isHr($user);
    }

    public function complete(User $user, TrainingEnrollment $trainingEnrollment): bool
    {
        return $this->hasPermission($user, 'complete_' . self::RESOURCE) && $this->isHr($user);
    }

    public function delete(User $user, TrainingEnrollment $trainingEnrollment): bool
    {
        return $this->hasPermission($user, 'delete_' . self::RESOURCE)
            && ($this->isHr($user)
            || ($this->owns($user, $trainingEnrollment->user_id) && $trainingEnrollment->status === 'ENROLLED'));
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, 'delete_any_' . self::RESOURCE) && $this->isHr($user);
    }
}
