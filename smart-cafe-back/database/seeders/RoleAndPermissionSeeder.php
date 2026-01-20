<?php

namespace Database\Seeders;

use App\Domain\User\Enumeration\UserPermissionEnum;
use App\Domain\User\Enumeration\UserRoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        foreach (UserPermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        // Re-register permissions after creation
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Admin role with all permissions
        $adminRole = Role::findOrCreate(UserRoleEnum::ADMIN->value, 'web');
        $adminRole->syncPermissions(Permission::all());

        // Create Manager role with manager permissions
        $managerRole = Role::findOrCreate(UserRoleEnum::MANAGER->value, 'web');
        $managerRole->syncPermissions(
            Permission::whereIn('name', array_map(fn ($p) => $p->value, UserPermissionEnum::managerPermissions()))->get()
        );

        // Create Employer role with employer permissions
        $employerRole = Role::findOrCreate(UserRoleEnum::EMPLOYER->value, 'web');
        $employerRole->syncPermissions(
            Permission::whereIn('name', array_map(fn ($p) => $p->value, UserPermissionEnum::employerPermissions()))->get()
        );
    }
}
