<?php

use App\Domain\User\Enumeration\UserRoleEnum;
use Illuminate\Support\Facades\Route;

$employeeRoles = implode('|', UserRoleEnum::values());

// Routes Employer (+ Admin et Manager via héritage)
Route::prefix('employee')->middleware(['role:'.$employeeRoles])->group(function () {
    // Les routes employee seront ajoutées ici (ex: consultation des commandes)
});
