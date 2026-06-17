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

/*
|--------------------------------------------------------------------------
| Member Contribution Summary
|--------------------------------------------------------------------------
*/

 $settings = $group->settings;
        $minimum = $settings?->minimum_contribution ?? 0;
        $penaltyAmount = $settings?->late_penalty_amount ?? 0;
        $penaltyType = $settings?->late_penalty_type ?? 'fixed';

$memberPayments = Contribution::with('user')
    ->where('group_id', $groupId)
    ->get()
    ->groupBy('user_id')
    ->map(function ($contributions) use ($minimum) {

        $user = $contributions->first()->user;

        $totalPaid = $contributions->sum('amount');

        $paidThisMonth = $contributions
            ->filter(function ($contribution) {
                return $contribution->paid_at->month === now()->month
                    && $contribution->paid_at->year === now()->year;
            })
            ->sum('amount');

        $remaining = max(
            0,
            $minimum - $paidThisMonth
        );

        return [
            'user' => $user,
            'total_paid' => $totalPaid,
            'paid_this_month' => $paidThisMonth,
            'remaining' => $remaining,
            'status' => $remaining <= 0
                ? 'Complete'
                : 'Pending',
        ];
    })
    ->sortBy('remaining');

        $userId = auth()->id();
       
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
        'memberPayments',
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