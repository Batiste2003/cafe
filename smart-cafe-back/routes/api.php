<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes pour les guests (non authentifiés)
require __DIR__.'/auth.php';

// Routes authentifiées
Route::middleware(['auth:sanctum'])->group(function () {
    // Route pour récupérer l'utilisateur connecté
    Route::get('/user', fn (Request $request) => $request->user());

    // Routes magasins accessibles (tous les utilisateurs authentifiés)
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'indexAccessible']);
        Route::get('/{store}', [StoreController::class, 'showAccessible']);
    });

    // Routes catégories accessibles (tous les utilisateurs authentifiés)
    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index']);
        Route::get('/{productCategory}', [ProductCategoryController::class, 'show']);
    });

    // Routes produits accessibles (tous les utilisateurs authentifiés)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'indexAccessible']);
        Route::get('/{product}', [ProductController::class, 'showAccessible']);
    });

    // Charger les routes par rôle
    require __DIR__.'/api/admin.php';
    require __DIR__.'/api/manager.php';
    require __DIR__.'/api/employee.php';
});