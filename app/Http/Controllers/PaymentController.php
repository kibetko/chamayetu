<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Group;

class PaymentController extends Controller
{
    public function index()
    {
        $groupId = session('active_group_id');

        $group = Group::findOrFail($groupId);

        $groups = auth()->user()
            ->groups()
            ->wherePivot('status', 'approved')
            ->get();

        $payments = Contribution::with([
    'user',
    'mpesaTransaction'
])
->where('group_id', $groupId)
->orderByDesc('paid_at')
->get()
->groupBy(function ($payment) {
    return $payment->paid_at->format('d M Y');
});

        $userId = auth()->id();
        $settings = $group->settings;
        $minimum = $settings?->minimum_contribution ?? 0;
$penaltyAmount = $settings?->late_penalty_amount ?? 0;
$penaltyType = $settings?->late_penalty_type ?? 'fixed';
$totalContributions = Contribution::where('group_id', $groupId)
    ->where('user_id', $userId)
    ->sum('amount');

        $minimumContribution = $minimum;
    $group->settings?->minimum_contribution ?? 0;

$dueDay =
    $group->settings?->contribution_due_day ?? 30;

$paidThisMonth = Contribution::where(
        'group_id',
        $groupId
    )
    ->where('user_id', auth()->id())
    ->whereMonth('paid_at', now()->month)
    ->whereYear('paid_at', now()->year)
    ->sum('amount');

$remainingThisMonth = max(
    0,
    $minimumContribution - $paidThisMonth
);

$dueDate = now()->copy()->day(
    min($dueDay, now()->daysInMonth)
);

if (now()->greaterThan($dueDate)) {
    $dueDate = $dueDate->addMonth();
}
$dueDate = now()->copy()->day($dueDay);

if ($dueDate->isPast()) {
    $dueDate->addMonth();
}

$isOverdue = now()->gt($dueDate);

$penalty = 0;

if ($isOverdue && $paidThisMonth < $minimum) {

    $shortfall = $minimum - $paidThisMonth;

    if ($penaltyType === 'percentage') {
        $penalty = ($shortfall * $penaltyAmount) / 100;
    } else {
        $penalty = $penaltyAmount;
    }
}
$remainingThisMonth = max($minimum - $paidThisMonth, 0);

$daysRemaining = now()->diffInDays($dueDate);

        $thisMonth = Contribution::where(
            'group_id',
            $groupId
        )
        ->whereMonth('paid_at', now()->month)
        ->whereYear('paid_at', now()->year)
        ->sum('amount');

        return view(
            'payments.index',
            compact(
    'group',
    'groups',
    'payments',
    'totalContributions',
    'thisMonth',
    'paidThisMonth',
    'remainingThisMonth',
    'dueDate',
    'daysRemaining',
    'penalty'
)
        );
    }
}