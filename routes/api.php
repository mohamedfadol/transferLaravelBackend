<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\TransactionController;

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

Route::prefix('auth')->as('auth.')->group(function(){
    Route::post('login',[AuthController::class, 'login'])->name('login'); 
    Route::post('register',[AuthController::class, 'register'])->name('register'); 
    Route::post('login-with-token',[AuthController::class, 'loginWithToken'])->middleware('auth:sanctum')->name('login_with_token'); 
    Route::get('logout',[AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout'); 
 });

 Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::group(['prefix' => 'transaction'], function () { 
        Route::post('get-all-user-transaction/{user_id}',[TransactionController::class, 'getAllUserTransaction'])->name('get.all.user.transaction'); 
        Route::post('make-transaction',[TransactionController::class, 'store'])->name('make.transaction'); 

        Route::group(['prefix' => 'users'], function () { 
            Route::get('get-users',[UserController::class, 'index']); 
        });
        
        Route::group(['prefix' => 'currencies'], function () { 
            Route::get('get-currencies',[CurrencyController::class, 'index']); 
            Route::get('add-currency',[CurrencyController::class, 'store']); 
        });

    });
});

 
