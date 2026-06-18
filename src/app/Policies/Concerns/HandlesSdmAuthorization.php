<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait HandlesSdmAuthorization
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    protected function isHr(User $user): bool
    {
        return $user->hasRole('admin_hr');
    }

    protected function isManager(User $user): bool
    {
        return $user->hasRole('manajer');
    }

    protected function isEmployee(User $user): bool
    {
        return $user->hasRole('karyawan');
    }

    protected function hasPermission(User $user, string $permission): bool
    {
        return $user->getAllPermissions()->contains('name', $permission);
    }

    protected function owns(User $user, int|string|null $ownerId): bool
    {
        return filled($ownerId) && (string) $user->getKey() === (string) $ownerId;
    }

    protected function managesUser(User $manager, User|int|string|null $employee): bool
    {
        if (! $this->isManager($manager) || blank($employee)) {
            return false;
        }

        if ($employee instanceof User) {
            return (string) $employee->manager_id === (string) $manager->getKey();
        }

        return User::query()
            ->whereKey($employee)
            ->where('manager_id', $manager->getKey())
            ->exists();
    }
}
