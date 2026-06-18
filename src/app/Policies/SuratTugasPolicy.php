<?php

namespace App\Policies;

use App\Models\SuratTugas;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class SuratTugasPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'surat::tugas';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, SuratTugas $suratTugas): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user)
            || $this->owns($user, $suratTugas->user_id)
            || $this->managesUser($user, $suratTugas->user_id));
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create_' . self::RESOURCE)
            && ($this->isHr($user) || $this->isManager($user));
    }

    public function update(User $user, SuratTugas $suratTugas): bool
    {
        return $this->hasPermission($user, 'update_' . self::RESOURCE)
            && ($this->isHr($user)
                || ($this->isManager($user) && $this->managesUser($user, $suratTugas->user_id)));
    }

    public function delete(User $user, SuratTugas $suratTugas): bool
    {
        if ($suratTugas->attendanceLogs()->exists()) {
            return false;
        }

        return $this->update($user, $suratTugas);
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, 'delete_any_' . self::RESOURCE) && $this->isHr($user);
    }
}
