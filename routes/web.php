<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\GroupUpdateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/groups/create', [GroupController::class, 'create'])
        ->name('groups.create');

    Route::post('/groups', [GroupController::class, 'store'])
        ->name('groups.store');

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');
    Route::get('/groups/switch/{group}', [
    GroupController::class,
    'switch'
])->name('groups.switch');
        Route::get('/groups', [
    GroupController::class,
    'index'
])->name('groups.index');



Route::get(
    '/groups/join',
    [GroupController::class, 'joinForm']
)->name('groups.join');

Route::post(
    '/groups/join',
    [GroupController::class, 'submitJoinRequest']
)->name('groups.join.submit');
});
Route::post(
    '/join-requests/{request}/approve',
    [GroupController::class, 'approveJoinRequest']
)->name('join-requests.approve');

Route::post(
    '/join-requests/{request}/reject',
    [GroupController::class, 'rejectJoinRequest']
)->name('join-requests.reject');

Route::get('/members', [
        MemberController::class,
        'index'
    ])->name('members.index');

Route::get(
    '/group/settings',
    [GroupController::class, 'settings']
)->name('groups.settings');

Route::post(
    '/group/settings',
    [GroupController::class, 'updateSettings']
)->name('groups.settings.update');

Route::get(
    '/help-center',
    [HelpCenterController::class, 'index']
)->name('help-center');

Route::post(
    '/help-center/contact',
    [HelpCenterController::class, 'storeRequest']
)->name('help-center.contact');
Route::post(
    '/group-updates',
    [GroupUpdateController::class, 'store']
)->name('group-updates.store');

Route::get(
    '/payments',
    [PaymentController::class, 'index']
)->name('payments.index');

Route::post(
    '/payments/stk-push',
    [MpesaController::class, 'stkPush']
)->name('mpesa.stk');

Route::post(
    '/payments/callback',
    [MpesaController::class, 'callback']
)->name('mpesa.callback');



require __DIR__.'/auth.php';