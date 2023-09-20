<?php

use App\Http\Controllers\Api\V1\AmortizationController;
use App\Http\Controllers\CaddyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/caddy-check', [CaddyController::class, 'check']);

# debugging cors
Route::options('/{any}', function() {
    \Log::info('Headers:', request()->headers->all());
    return response('', 200);
})->where('any', '.*');
