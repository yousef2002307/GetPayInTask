<?php

use App\Http\Controllers\Api\ProductController\ProductController;
use App\Http\Controllers\Api\HoldController\HoldController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/available-stock', [HoldController::class, 'availableStock']);
});

Route::prefix('holds')->group(function () {
    Route::post('/', [HoldController::class, 'store']);
});
