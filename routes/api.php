<?php

use App\Http\Controllers\Api\V1\PriceComparisonController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('price-comparison', [PriceComparisonController::class, 'compare']);
});
