<?php

use App\Http\Controllers\Api\ProductController\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/{id}', [ProductController::class, 'show']);
});
