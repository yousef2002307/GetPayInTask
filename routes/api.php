<?php

use App\Http\Controllers\Api\ProductController\ProductController;
use App\Http\Controllers\Api\HoldController\HoldController;
use App\Http\Controllers\Api\OrderController\OrderController;
use App\Http\Controllers\Api\WebhookController\WebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureIdempotency;

Route::prefix('products')->group(function () {
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/available-stock', [HoldController::class, 'availableStock']);
});

Route::prefix('holds')->group(function () {
    Route::post('/', [HoldController::class, 'store']);
});

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
});

Route::prefix('payments/webhook')->group(function () {
    Route::post('/', [WebhookController::class, 'index'])->middleware([
        EnsureIdempotency::class,
    ]);
});

