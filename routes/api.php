<?php

use App\Http\Controllers\Api\V1\AmortizationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/pay-all-due-amortizations', [AmortizationController::class, 'payAllDueAmortizations']);
});

Route::prefix('v1')->group(function () {
    Route::get('/index', [AmortizationController::class, 'index']);
});
Route::prefix('v1')->group(function () {
    Route::post('/check-batch-status', [AmortizationController::class, 'checkBatchStatus']);

});