<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\ParserController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix("/currencies")->group(function () {
    Route::get('/', [CurrenciesController::class, 'getAll']);
    Route::get('/{id}', [CurrenciesController::class, 'getInfoById'])->where(["id" => "[a-z0-9]{40}"]);
});

Route::prefix("/products")->group(function () {
    Route::get('/', [ProductsController::class, 'getAll']);
});

//work
Route::prefix("/parse")->group(function () {
    Route::get('/', [ParserController::class, 'parse']);
});

