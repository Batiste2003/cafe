<?php

namespace Database\Seeders;

use App\Domain\Store\Enumeration\StoreStatusEnum;
use App\Domain\User\Enumeration\UserRoleEnum;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role(UserRoleEnum::ADMIN->value)->first();
        $manager = User::role(UserRoleEnum::MANAGER->value)->first();
        $employer = User::role(UserRoleEnum::EMPLOYER->value)->first();

        if (! $admin) {
            return;
        }

        // Créer un magasin de démonstration
        $store = Store::create([
            'name' => 'Café Central',
            'status' => StoreStatusEnum::ACTIVE->value,
            'created_by' => $admin->id,
        ]);

        // Associer le manager et l'employé au magasin
        if ($manager) {
            $store->users()->attach($manager->id);
        }

        if ($employer) {
            $store->users()->attach($employer->id);
        }

        // Créer un deuxième magasin (brouillon)
        $store2 = Store::create([
            'name' => 'Café du Parc',
            'status' => StoreStatusEnum::DRAFT->value,
            'created_by' => $admin->id,
        ]);

        // Associer uniquement le manager au deuxième magasin
        if ($manager) {
            $store2->users()->attach($manager->id);
        }
    }
}
