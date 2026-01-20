<?php

namespace Database\Seeders;

use App\Domain\User\Enumeration\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@cafe.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('AdminPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole(UserRoleEnum::ADMIN->value)) {
            $admin->assignRole(UserRoleEnum::ADMIN->value);
        }

        // Créer un utilisateur Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@cafe.local'],
            [
                'name' => 'Manager',
                'password' => Hash::make('ManagerPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        if (! $manager->hasRole(UserRoleEnum::MANAGER->value)) {
            $manager->assignRole(UserRoleEnum::MANAGER->value);
        }

        // Créer un utilisateur Employer
        $employer = User::firstOrCreate(
            ['email' => 'employer@cafe.local'],
            [
                'name' => 'Employer',
                'password' => Hash::make('EmployerPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        if (! $employer->hasRole(UserRoleEnum::EMPLOYER->value)) {
            $employer->assignRole(UserRoleEnum::EMPLOYER->value);
        }
    }
}
