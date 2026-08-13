<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\DashboardController;

Route::post(
    '/login',
    [MobileAuthController::class,'login']
);


Route::post(
    '/mpesa/callback',
    [MpesaController::class,'callback']
);


Route::middleware('auth:sanctum')->group(function(){

    Route::post(
        '/logout',
        [MobileAuthController::class,'logout']
    );


    Route::get(
        '/user',
        [MobileAuthController::class,'user']
    );

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ]);

    Route::get(
        '/members',
        [MemberController::class, 'index']
    )->name('api.members.index');

});