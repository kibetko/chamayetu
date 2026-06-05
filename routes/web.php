<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
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

require __DIR__.'/auth.php';