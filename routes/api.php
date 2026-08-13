<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MpesaController;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MobileLoanController;
use App\Http\Controllers\Api\MobilePaymentController;
use App\Http\Controllers\Api\GroupSettingsController;
use App\Http\Controllers\Api\NotificationController;


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

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [MobileAuthController::class, 'logout']
    );

    Route::get(
        '/user',
        [MobileAuthController::class, 'user']
    );


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | MEMBERS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/members',
        [MemberController::class, 'index']
    )->name('api.members.index');


    /*
    |--------------------------------------------------------------------------
    | LOANS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/loans',
        [MobileLoanController::class, 'index']
    );

    Route::post(
        '/loans',
        [MobileLoanController::class, 'store']
    );

    Route::get(
        '/loans/{loan}',
        [MobileLoanController::class, 'show']
    );

    Route::post(
        '/loans/{loan}/repay',
        [MobileLoanController::class, 'repay']
    );


    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payments',
        [MobilePaymentController::class, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | GROUP SETTINGS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/group-settings',
        [GroupSettingsController::class, 'index']
    )->name('api.group-settings.index');

    Route::put(
        '/group-settings',
        [GroupSettingsController::class, 'update']
    )->name('api.group-settings.update');

    Route::put(
        '/group-settings/leadership',
        [GroupSettingsController::class, 'updateLeadership']
    )->name('api.group-settings.leadership');

    Route::get(
    '/notifications',
    [NotificationController::class, 'index']
);

Route::patch(
    '/notifications/{notification}/read',
    [NotificationController::class, 'markAsRead']
);

Route::post(
    '/notifications/read-all',
    [NotificationController::class, 'markAllAsRead']
);

});