<?php

use App\Domain\User\Enumeration\UserRoleEnum;
use Illuminate\Support\Facades\Route;

$managerRoles = UserRoleEnum::ADMIN->value.'|'.UserRoleEnum::MANAGER->value;

// Routes Manager (+ Admin via héritage de permissions)
Route::prefix('manager')->middleware(['role:'.$managerRoles])->group(function () {
    // Les routes manager seront ajoutées ici (ex: gestion des commandes)
});
