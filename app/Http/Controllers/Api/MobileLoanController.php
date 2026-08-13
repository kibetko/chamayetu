<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\Loan;
use App\Models\LoanApproval;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;

class MobileLoanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET LOANS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $groupId = $request->input('group_id');

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        if (!$groupId) {
            return response()->json([
                'success' => false,
                'message' => 'group_id is required.',
            ], 422);
        }

        $group = $user->groups()
            ->wherePivot('status', 'active')
            ->where('groups.id', $groupId)
            ->with('settings')
            ->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP FUNDS
        |--------------------------------------------------------------------------
        */

        $totalContributions = $group->total_contributions;

        $totalLoaned = $group->total_loaned;

        $totalRepayments = LoanRepayment::whereHas('loan', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->sum('amount');

        $totalDisbursed = Loan::where('group_id', $groupId)
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue',
            ])
            ->sum('amount');

        $availableFunds = max(
            0,
            $totalContributions
            + $totalRepayments
            - $totalDisbursed
        );

        /*
        |--------------------------------------------------------------------------
        | MY LOANS
        |--------------------------------------------------------------------------
        */

        $myLoans = Loan::with([
            'repayments',
            'approvals.approver',
        ])
            ->where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROUP LOANS
        |--------------------------------------------------------------------------
        */

        $groupLoans = Loan::with([
            'user',
            'approvals.approver',
            'repayments',
        ])
            ->where('group_id', $groupId)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROUP MEMBER ROLE
        |--------------------------------------------------------------------------
        */

        $membership = $group->members()
            ->where('users.id', $user->id)
            ->first();

        $role = $membership?->pivot?->role;

        $isOfficial = in_array(
            strtolower($role ?? ''),
            [
                'chairperson',
                'secretary',
                'treasurer',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $myTotalBorrowed = $myLoans->sum(function ($loan) {
            return (float) $loan->amount;
        });

        $myTotalRepaid = $myLoans->sum(function ($loan) {
            return $loan->repayments->sum('amount');
        });

        $myOutstanding = $myLoans->sum(function ($loan) {
            return max(
                0,
                (float) ($loan->total_payable ?? $loan->amount)
                - (float) $loan->repayments->sum('amount')
            );
        });

        $activeMyLoans = $myLoans
            ->whereIn('status', [
                'pending',
                'approved',
                'disbursed',
                'overdue',
            ])
            ->count();

        $pendingRequests = $groupLoans
            ->where('status', 'pending')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            /*
            |--------------------------------------------------------------------------
            | GROUP
            |--------------------------------------------------------------------------
            */

            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'unique_code' => $group->unique_code,
            ],

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $role,
                'is_official' => $isOfficial,
            ],

            /*
            |--------------------------------------------------------------------------
            | FUNDS
            |--------------------------------------------------------------------------
            */

            'funds' => [
                'total_contributions' => (float) $totalContributions,
                'total_loaned' => (float) $totalLoaned,
                'total_repaid' => (float) $totalRepayments,
                'available' => (float) $availableFunds,
            ],

            /*
            |--------------------------------------------------------------------------
            | MY STATS
            |--------------------------------------------------------------------------
            */

            'my_stats' => [
                'total_borrowed' => (float) $myTotalBorrowed,
                'total_repaid' => (float) $myTotalRepaid,
                'outstanding' => (float) $myOutstanding,
                'active_loans' => $activeMyLoans,
                'loan_count' => $myLoans->count(),
            ],

            /*
            |--------------------------------------------------------------------------
            | GROUP STATS
            |--------------------------------------------------------------------------
            */

            'group_stats' => [
                'total_loans' => $groupLoans->count(),
                'pending_requests' => $pendingRequests,
                'active_loans' => $groupLoans
                    ->whereIn('status', [
                        'approved',
                        'disbursed',
                        'overdue',
                    ])
                    ->count(),
                'completed_loans' => $groupLoans
                    ->where('status', 'completed')
                    ->count(),
                'overdue_loans' => $groupLoans
                    ->where('status', 'overdue')
                    ->count(),
            ],

            /*
            |--------------------------------------------------------------------------
            | MY LOANS
            |--------------------------------------------------------------------------
            */

            'my_loans' => $myLoans
                ->map(function ($loan) {
                    return $this->formatLoan($loan);
                })
                ->values(),

            /*
            |--------------------------------------------------------------------------
            | GROUP LOANS
            |--------------------------------------------------------------------------
            */

            'group_loans' => $groupLoans
                ->map(function ($loan) {
                    return $this->formatLoan($loan, true);
                })
                ->values(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY FOR LOAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDATE BASIC REQUEST
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'group_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1',
            'reason' => 'required|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $group = $user->groups()
            ->wherePivot('status', 'active')
            ->where('groups.id', $request->group_id)
            ->with('settings')
            ->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE FUNDS
        |--------------------------------------------------------------------------
        */

        $totalContributions = $group->total_contributions;

        $totalLoaned = $group->total_loaned;

        $totalRepayments = LoanRepayment::whereHas('loan', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->sum('amount');

        $availableFunds = max(
            0,
            $totalContributions
            + $totalRepayments
            - $totalLoaned
        );

        /*
        |--------------------------------------------------------------------------
        | CHECK AVAILABLE FUNDS
        |--------------------------------------------------------------------------
        */

        if ($request->amount > $availableFunds) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Maximum loan available is KES ' .
                    number_format($availableFunds),
                'available_funds' => (float) $availableFunds,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | USER CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $userContributions = Contribution::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        if ($userContributions <= 0) {
            return response()->json([
                'success' => false,
                'message' =>
                    'You must make contributions before applying for a loan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | PERSONAL LOAN LIMIT
        |--------------------------------------------------------------------------
        */

        $multiplier =
            $group->settings->maximum_loan_multiplier ?? 1;

        $maxLoan =
            $userContributions * $multiplier;

        if ($request->amount > $maxLoan) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Your maximum loan limit is KES ' .
                    number_format($maxLoan),
                'maximum_loan' => (float) $maxLoan,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | EXISTING ACTIVE LOAN
        |--------------------------------------------------------------------------
        */

        $activeLoan = Loan::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'pending',
                'approved',
                'disbursed',
                'overdue',
            ])
            ->exists();

        if ($activeLoan) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active loan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | MAX REPAYMENT PERIOD
        |--------------------------------------------------------------------------
        */

        $maxDays =
            $group->settings->repayment_period_days ?? 12;

        if ($request->duration_days > $maxDays) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Maximum repayment period is ' .
                    $maxDays .
                    ' days.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | INTEREST
        |--------------------------------------------------------------------------
        */

        $interestRate =
            $group->settings->interest_rate ?? 0;

        $months = (int) $request->duration_days;

        $interestAmount =
            $request->amount *
            ($interestRate / 100) *
            ($months / 30);

        $totalPayable =
            $request->amount +
            $interestAmount;

        /*
        |--------------------------------------------------------------------------
        | CREATE LOAN
        |--------------------------------------------------------------------------
        */

        $loan = Loan::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'total_payable' => $totalPayable,
            'interest_rate' => $interestRate,
            'duration_days' => $months,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $loan->load([
            'repayments',
            'approvals.approver',
        ]);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Loan request submitted successfully.',

            'group' => [
                'id' => $group->id,
                'name' => $group->name,
            ],

            'loan' => $this->formatLoan($loan),
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW SINGLE LOAN
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, Loan $loan)
    {
        $user = $request->user();

        $belongsToGroup = $user->groups()
            ->wherePivot('status', 'active')
            ->where('groups.id', $loan->group_id)
            ->exists();

        if (!$belongsToGroup) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this loan.',
            ], 403);
        }

        $loan->load([
            'user',
            'repayments',
            'approvals.approver',
            'group',
        ]);

        return response()->json([
            'success' => true,

            'group' => [
                'id' => $loan->group->id,
                'name' => $loan->group->name,
            ],

            'loan' => $this->formatLoan($loan, true),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | REPAY LOAN
    |--------------------------------------------------------------------------
    */

    public function repay(Request $request, Loan $loan)
    {
        $user = $request->user();

        if ($loan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only repay your own loan.',
            ], 403);
        }

        if (!in_array($loan->status, [
            'disbursed',
            'overdue',
        ])) {
            return response()->json([
                'success' => false,
                'message' =>
                    'This loan cannot currently receive repayments.',
            ], 422);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $remaining = max(
            0,
            (float) $loan->total_payable
            - (float) $loan->repayments()->sum('amount')
        );

        if ($request->amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Repayment cannot exceed the outstanding balance.',
                'outstanding' => $remaining,
            ], 422);
        }

        LoanRepayment::create([
            'loan_id' => $loan->id,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_method' => 'manual',
            'paid_at' => now(),
        ]);

        $newRemaining =
            $remaining - $request->amount;

        if ($newRemaining <= 0) {
            $loan->update([
                'status' => 'completed',
            ]);
        }

        $loan->load('repayments');

        return response()->json([
            'success' => true,
            'message' => 'Repayment recorded successfully.',

            'loan' => $this->formatLoan($loan),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT LOAN
    |--------------------------------------------------------------------------
    */

    private function formatLoan(
        Loan $loan,
        bool $includeUser = false
    ) {
        $totalPayable =
            (float) (
                $loan->total_payable
                ?? $loan->amount
            );

        $totalRepaid =
            (float) $loan->repayments->sum('amount');

        $outstanding =
            max(
                0,
                $totalPayable - $totalRepaid
            );

        return [

            'id' => $loan->id,

            'group_id' => $loan->group_id,

            'group_name' =>
                $loan->group?->name,

            'amount' =>
                (float) $loan->amount,

            'interest_rate' =>
                (float) ($loan->interest_rate ?? 0),

            'total_payable' =>
                $totalPayable,

            'total_repaid' =>
                $totalRepaid,

            'outstanding' =>
                $outstanding,

            'duration_days' =>
                $loan->duration_days,

            'reason' =>
                $loan->reason,

            'status' =>
                $loan->status,

            'approved_at' =>
                $loan->approved_at,

            'disbursed_at' =>
                $loan->disbursed_at,

            'due_date' =>
                $loan->due_date,

            'created_at' =>
                $loan->created_at,

            'repayments' =>
                $loan->repayments->map(function ($repayment) {
                    return [
                        'id' => $repayment->id,
                        'amount' => (float) $repayment->amount,
                        'payment_method' =>
                            $repayment->payment_method,
                        'paid_at' =>
                            $repayment->paid_at,
                    ];
                })->values(),

            'approvals' =>
                $loan->approvals->map(function ($approval) {
                    return [
                        'id' => $approval->id,
                        'decision' => $approval->decision,
                        'comment' => $approval->comment,
                        'approved_at' =>
                            $approval->approved_at,

                        'approver' => $approval->approver
                            ? [
                                'id' =>
                                    $approval->approver->id,
                                'name' =>
                                    $approval->approver->name,
                            ]
                            : null,
                    ];
                })->values(),

            'user' => $includeUser && $loan->user
                ? [
                    'id' => $loan->user->id,
                    'name' => $loan->user->name,
                    'email' => $loan->user->email,
                ]
                : null,
        ];
    }
}