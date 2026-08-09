<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\GroupUpdateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanRepaymentController;
use App\Http\Controllers\NotificationController;
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

    Route::get('/groups/join', [GroupController::class, 'joinForm'])->name('groups.join');
    Route::post('/groups/join', [GroupController::class, 'submitJoinRequest'])->name('groups.join.submit');

    // group-scoped members list (this fixes the missing groups.members.index route)
    Route::get('/groups/{group}/members', [MemberController::class, 'index'])
        ->name('groups.members.index');

    // unscoped members list
    Route::get('/members', [MemberController::class, 'index'])
        ->name('members.index');

    // member actions (group scoped)
    Route::get('/groups/{group}/members/{member}', [MemberController::class, 'show'])
        ->name('members.show');

    Route::get('/groups/{group}/members/{member}/edit', [MemberController::class, 'edit'])
        ->name('members.edit');

    Route::put('/groups/{group}/members/{member}', [MemberController::class, 'update'])
        ->name('members.update');

    Route::get('/groups/{group}/members/export', [MemberController::class, 'export'])
        ->name('groups.members.export');

    Route::get('/groups/{group}/members/invite', [MemberController::class, 'invite'])
        ->name('members.invite');

    Route::post('/groups/{group}/members/invite', [MemberController::class, 'inviteStore'])
        ->name('members.invite.store');

});
Route::post(
    '/join-requests/{request}/approve',
    [GroupController::class, 'approveJoinRequest']
)->name('join-requests.approve');

Route::post('/loans/{loan}/reject',
    [LoanController::class,'reject']
)->name('loans.reject');

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
    '/mpesa/callback',
    [MpesaController::class, 'callback']
)->name('mpesa.callback');

Route::get('/loans', [
    LoanController::class,
    'index'
])->name('loans.index');

Route::get('/loans/apply', [
    LoanController::class,
    'apply'
])->name('loans.apply');

Route::post('/loans/store', [
    LoanController::class,
    'store'
])->name('loans.store');

Route::post('/loans/{loan}/approve', [
    LoanController::class,
    'approve'
])->name('loans.approve');

Route::post('/loans/{loan}/disburse', [
    LoanController::class,
    'disburse'
])->name('loans.disburse');




Route::post(
    '/notifications/{notification}/read',
    [NotificationController::class, 'markAsRead']
)->name('notifications.read');

Route::post(
'/loans/{loan}/repay',
[LoanRepaymentController::class,'store']
)
->name('loans.repay');

Route::get('/loans/{loan}', [LoanController::class,'show'])
    ->name('loans.show');

require __DIR__.'/auth.php';