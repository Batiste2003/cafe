<?php

use App\Domain\User\Enumeration\UserRoleEnum;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOptionController;
use App\Http\Controllers\ProductOptionValueController;
use App\Http\Controllers\ProductStoreController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes Admin uniquement
Route::prefix('admin')->middleware(['role:'.UserRoleEnum::ADMIN->value])->group(function () {
    // Gestion des utilisateurs
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show'])->withTrashed();
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/restore', [UserController::class, 'restore'])->withTrashed();
    });

    // Gestion des adresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::get('/{address}', [AddressController::class, 'show']);
        Route::put('/{address}', [AddressController::class, 'update']);
        Route::delete('/{address}', [AddressController::class, 'destroy']);
    });

    // Gestion des magasins
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{store}', [StoreController::class, 'show'])->withTrashed();
        Route::put('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'destroy']);
        Route::post('/{store}/users', [StoreController::class, 'attachUsers']);
        Route::delete('/{store}/users/{user}', [StoreController::class, 'detachUser']);
    });

    // Gestion des catégories de produits
    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index']);
        Route::post('/', [ProductCategoryController::class, 'store']);
        Route::get('/{productCategory}', [ProductCategoryController::class, 'show'])->withTrashed();
        Route::put('/{productCategory}', [ProductCategoryController::class, 'update']);
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy']);
    });

    // Gestion des produits
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{product}', [ProductController::class, 'show'])->withTrashed();
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
        Route::post('/{product}/gallery', [ProductController::class, 'attachGallery']);
        Route::delete('/{product}/gallery/{storedFile}', [ProductController::class, 'detachGallery']);

        // Variants nested routes
        Route::prefix('{product}/variants')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index']);
            Route::post('/', [ProductVariantController::class, 'store']);
            Route::get('/{variant}', [ProductVariantController::class, 'show'])->withTrashed();
            Route::put('/{variant}', [ProductVariantController::class, 'update']);
            Route::delete('/{variant}', [ProductVariantController::class, 'destroy']);
            Route::post('/{variant}/gallery', [ProductVariantController::class, 'attachGallery']);
            Route::delete('/{variant}/gallery/{storedFile}', [ProductVariantController::class, 'detachGallery']);
        });

        // Options nested routes
        Route::prefix('{product}/options')->group(function () {
            Route::get('/', [ProductOptionController::class, 'index']);
            Route::post('/', [ProductOptionController::class, 'store']);
            Route::get('/{option}', [ProductOptionController::class, 'show']);
            Route::put('/{option}', [ProductOptionController::class, 'update']);
            Route::delete('/{option}', [ProductOptionController::class, 'destroy']);
        });

        // Stores nested routes (association produit-store)
        Route::prefix('{product}/stores')->group(function () {
            Route::get('/', [ProductStoreController::class, 'indexStores']);
            Route::post('/', [ProductStoreController::class, 'attachStores']);
            Route::delete('/{store}', [ProductStoreController::class, 'detachStore']);
        });

        // Variant stocks nested routes (stock par variant par store)
        Route::prefix('{product}/variants/{variant}/stocks')->group(function () {
            Route::get('/', [ProductStoreController::class, 'indexVariantStocks']);
            Route::put('/{store}', [ProductStoreController::class, 'updateVariantStock']);
            Route::delete('/{store}', [ProductStoreController::class, 'deleteVariantStock']);
        });
    });

    // Gestion des valeurs d'options (route séparée car liée à l'option, pas au produit)
    Route::prefix('product-options/{option}/values')->group(function () {
        Route::get('/', [ProductOptionValueController::class, 'index']);
        Route::post('/', [ProductOptionValueController::class, 'store']);
        Route::get('/{value}', [ProductOptionValueController::class, 'show']);
        Route::put('/{value}', [ProductOptionValueController::class, 'update']);
        Route::delete('/{value}', [ProductOptionValueController::class, 'destroy']);
    });
});
