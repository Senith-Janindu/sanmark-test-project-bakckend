<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MeterReaderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix'=>'meterReader',
], function (){
    Route::post('/login',[MeterReaderController::class, 'login']);
    Route::group([
        'middleware' => 'auth:sanctum'
    ], function () {
        Route::post('/addCustomerDetails',[MeterReaderController::class, 'addCustomerDetails']);
    });
});

Route::group([
    'prefix'=>'customer',
], function (){
    Route::post('/getCustomerDetails',[CustomerController::class, 'getCustomerDetails']);
});
