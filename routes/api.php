<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;


Route::post(
    '/login',
    [MobileAuthController::class,'login']
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

});