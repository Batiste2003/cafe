<?php

namespace App\Domain\User\Enumeration;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case EMPLOYER = 'employer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::MANAGER => 'Manager',
            self::EMPLOYER => 'Employé',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isManager(): bool
    {
        return $this === self::MANAGER;
    }

    public function isEmployer(): bool
    {
        return $this === self::EMPLOYER;
    }

    /**
     * Returns roles that Admin can create (excludes Admin).
     *
     * @return array<self>
     */
    public static function creatableByAdmin(): array
    {
        return [self::MANAGER, self::EMPLOYER];
    }

    /**
     * Returns all role values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
