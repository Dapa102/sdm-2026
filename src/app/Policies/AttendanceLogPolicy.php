<?php

namespace App\Policies;

use App\Models\AttendanceLog;
use App\Models\User;
use App\Policies\Concerns\HandlesSdmAuthorization;

class AttendanceLogPolicy
{
    use HandlesSdmAuthorization;

    private const RESOURCE = 'attendance::log';

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view_any_' . self::RESOURCE);
    }

    public function view(User $user, AttendanceLog $attendanceLog): bool
    {
        return $this->hasPermission($user, 'view_' . self::RESOURCE)
            && ($this->isHr($user)
            || $this->owns($user, $attendanceLog->user_id)
            || $this->managesUser($user, $attendanceLog->user_id));
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create_' . self::RESOURCE) && $this->isEmployee($user);
    }

    public function update(User $user, AttendanceLog $attendanceLog): bool
    {
        return $this->hasPermission($user, 'update_' . self::RESOURCE)
            && ($this->owns($user, $attendanceLog->user_id)
            || $this->isHr($user)
            || $this->managesUser($user, $attendanceLog->user_id));
    }

    public function approve(User $user, AttendanceLog $attendanceLog): bool
    {
        return $this->hasPermission($user, 'approve_' . self::RESOURCE)
            && ($this->isHr($user) || $this->managesUser($user, $attendanceLog->user_id));
    }

    public function reject(User $user, AttendanceLog $attendanceLog): bool
    {
        return $this->approve($user, $attendanceLog);
    }

    public function delete(User $user, AttendanceLog $attendanceLog): bool
    {
        return $this->hasPermission($user, 'delete_' . self::RESOURCE) && $this->isHr($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, 'delete_any_' . self::RESOURCE) && $this->isHr($user);
    }
}
