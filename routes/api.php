<?php

use App\Http\Controllers\AirCmsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MosecomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/parse/{name?}', [MosecomController::class, 'parse'])->name('api.parse');
Route::get('/devices', [AirCmsController::class, 'getDevices'])->name('api.aircms.devices');

Route::get('/records/{date}', [MosecomController::class, 'getRecordByDate']);


