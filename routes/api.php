<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);


Route::group(['middleware' => ['auth:api']], function () {

    Route::post('transactions', [TransactionController::class, 'store']);
    Route::get('transactions/report', [TransactionController::class, 'report']);

    Route::middleware('role:Admin')->group(function () {

        Route::get('user', function () {
            return auth()->user();
        });

        Route::post('register', [AuthController::class, 'register']);

        //-- Master Product
        Route::apiResource('products', ProductController::class);

    });

});
