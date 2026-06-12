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
    'transaction'
])
->where('group_id', $groupId)
->orderByDesc('paid_at')
->get()
->groupBy(function ($payment) {
    return $payment->paid_at->format('d M Y');
});

        $totalContributions = Contribution::where(
            'group_id',
            $groupId
        )->sum('amount');

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
                'thisMonth'
            )
        );
    }
}