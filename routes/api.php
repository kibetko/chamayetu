<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MpesaController;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MobileLoanController;
use App\Http\Controllers\Api\MobilePaymentController;


/*
|--------------------------------------------------------------------------
| MOBILE AUTH
|--------------------------------------------------------------------------
*/

Route::post(
    '/login',
    [MobileAuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| M-PESA CALLBACK
|--------------------------------------------------------------------------
|
| Safaricom calls this endpoint.
| It must NOT require Sanctum authentication.
|
*/

Route::post(
    '/mpesa/callback',
    [MpesaController::class, 'callback']
);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED MOBILE API
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

   Route::middleware('auth:sanctum')->group(function(){

    Route::post(
        '/logout',
        [MobileAuthController::class,'logout']
    );

    Route::get(
        '/user',
        [MobileAuthController::class,'user']
    );

    Route::get(
        '/dashboard',
        [DashboardController::class,'index']
    );

    Route::get(
        '/members',
        [MemberController::class,'index']
    )->name('api.members.index');

    Route::get(
        '/loans',
        [MobileLoanController::class,'index']
    );

    Route::post(
        '/loans',
        [MobileLoanController::class,'store']
    );

    Route::get(
        '/loans/{loan}',
        [MobileLoanController::class,'show']
    );

    Route::post(
        '/loans/{loan}/repay',
        [MobileLoanController::class,'repay']
    );

    /*
    |--------------------------------------------------------------------------
    | MOBILE PAYMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payments',
        [MobilePaymentController::class,'index']
    );
});
});