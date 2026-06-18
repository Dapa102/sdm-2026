<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    private const RESOURCE_PERMISSIONS = [
        'view',
        'view_any',
        'create',
        'update',
        'delete',
        'delete_any',
    ];

    private const RESOURCES = [
        'user',
        'role',
        'activity',
        'surat::tugas',
        'attendance::log',
        'merit::transaction',
        'reward::catalog',
        'reward::request',
        'training',
        'training::enrollment',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(self::RESOURCES)
            ->flatMap(fn (string $resource) => collect(self::RESOURCE_PERMISSIONS)
                ->map(fn (string $prefix) => "{$prefix}_{$resource}"))
            ->values();

        $permissions
            ->merge([
                'approve_attendance::log',
                'reject_attendance::log',
                'approve_reward::request',
                'reject_reward::request',
                'complete_training::enrollment',
            ])
            ->unique()
            ->each(fn (string $permission) => Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web'],
            ));

        Role::findByName('super_admin')->syncPermissions(Permission::all());

        Role::findByName('admin_hr')->syncPermissions($this->adminHrPermissions());
        Role::findByName('manajer')->syncPermissions($this->managerPermissions());
        Role::findByName('karyawan')->syncPermissions($this->employeePermissions());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<int, string>
     */
    private function adminHrPermissions(): array
    {
        return [
            ...$this->permissionsFor('surat::tugas'),
            'view_attendance::log',
            'view_any_attendance::log',
            'update_attendance::log',
            'approve_attendance::log',
            'reject_attendance::log',
            'view_merit::transaction',
            'view_any_merit::transaction',
            ...$this->permissionsFor('reward::catalog'),
            'view_reward::request',
            'view_any_reward::request',
            'update_reward::request',
            'approve_reward::request',
            'reject_reward::request',
            ...$this->permissionsFor('training'),
            'view_training::enrollment',
            'view_any_training::enrollment',
            'update_training::enrollment',
            'complete_training::enrollment',
            'delete_training::enrollment',
            'delete_any_training::enrollment',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function managerPermissions(): array
    {
        return [
            'view_surat::tugas',
            'view_any_surat::tugas',
            'create_surat::tugas',
            'update_surat::tugas',
            'delete_surat::tugas',
            'view_attendance::log',
            'view_any_attendance::log',
            'update_attendance::log',
            'approve_attendance::log',
            'reject_attendance::log',
            'view_merit::transaction',
            'view_any_merit::transaction',
            'view_reward::catalog',
            'view_any_reward::catalog',
            'view_reward::request',
            'view_any_reward::request',
            'create_reward::request',
            'update_reward::request',
            'approve_reward::request',
            'reject_reward::request',
            'view_training',
            'view_any_training',
            'view_training::enrollment',
            'view_any_training::enrollment',
            'create_training::enrollment',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function employeePermissions(): array
    {
        return [
            'view_surat::tugas',
            'view_any_surat::tugas',
            'view_attendance::log',
            'view_any_attendance::log',
            'create_attendance::log',
            'update_attendance::log',
            'view_merit::transaction',
            'view_any_merit::transaction',
            'view_reward::catalog',
            'view_any_reward::catalog',
            'view_reward::request',
            'view_any_reward::request',
            'create_reward::request',
            'update_reward::request',
            'delete_reward::request',
            'view_training',
            'view_any_training',
            'view_training::enrollment',
            'view_any_training::enrollment',
            'create_training::enrollment',
            'delete_training::enrollment',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function permissionsFor(string $resource): array
    {
        return collect(self::RESOURCE_PERMISSIONS)
            ->map(fn (string $prefix) => "{$prefix}_{$resource}")
            ->all();
    }
}
