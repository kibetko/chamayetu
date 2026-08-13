<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\LoanRepayment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MobilePaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAYMENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $groupId = $request->query('group_id');

        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        \Log::info('MOBILE PAYMENTS REQUEST', [
            'user_id' => $user?->id,
            'group_id' => $groupId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDATE GROUP
        |--------------------------------------------------------------------------
        */

        if (!$groupId) {
            return response()->json([
                'success' => false,
                'message' => 'Group ID is required.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | FIND GROUP
        |--------------------------------------------------------------------------
        */

        $group = Group::find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK MEMBERSHIP
        |--------------------------------------------------------------------------
        |
        | Your database appears to use "active".
        | We therefore accept both active and approved.
        |
        */

        $membership = $user->groups()
            ->where('groups.id', $groupId)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to this group.',
                'debug' => [
                    'user_id' => $user->id,
                    'group_id' => (int) $groupId,
                    'status' => null,
                ],
            ], 403);
        }

        $membershipStatus = $membership->pivot->status ?? null;

        if (!in_array(
            strtolower((string) $membershipStatus),
            ['active', 'approved'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not approved.',
                'debug' => [
                    'user_id' => $user->id,
                    'group_id' => (int) $groupId,
                    'status' => $membershipStatus,
                ],
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings = $group->settings;

        $minimumContribution =
            (float) ($settings?->minimum_contribution ?? 0);

        $dueDay =
            (int) ($settings?->contribution_due_day ?? 30);

        /*
        |--------------------------------------------------------------------------
        | USER'S CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $totalContributions = Contribution::where(
            'group_id',
            $group->id
        )
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                'paid'
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | USER'S CONTRIBUTIONS THIS MONTH
        |--------------------------------------------------------------------------
        */

        $paidThisMonth = Contribution::where(
            'group_id',
            $group->id
        )
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                'paid'
            )
            ->whereMonth(
                'paid_at',
                now()->month
            )
            ->whereYear(
                'paid_at',
                now()->year
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | REMAINING CONTRIBUTION
        |--------------------------------------------------------------------------
        */

        $remainingThisMonth = max(
            0,
            $minimumContribution - $paidThisMonth
        );

        /*
        |--------------------------------------------------------------------------
        | DUE DATE
        |--------------------------------------------------------------------------
        */

        $safeDueDay = min(
            max($dueDay, 1),
            now()->daysInMonth
        );

        $dueDate = now()
            ->copy()
            ->day($safeDueDay);

        if ($dueDate->isPast()) {

            $nextMonth = now()
                ->copy()
                ->addMonth();

            $safeNextDueDay = min(
                max($dueDay, 1),
                $nextMonth->daysInMonth
            );

            $dueDate = $nextMonth
                ->day($safeNextDueDay);
        }

        /*
        |--------------------------------------------------------------------------
        | DAYS REMAINING
        |--------------------------------------------------------------------------
        */

        $daysRemaining = now()->diffInDays(
            $dueDate,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | CONTRIBUTION PAYMENTS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | These are the actual contribution records.
        |
        | The name comes from:
        |
        | contribution -> user -> name
        |
        */

        $contributionPayments = Contribution::with([
            'user',
            'mpesaTransaction',
        ])
            ->where(
                'group_id',
                $group->id
            )
            ->where(
                'status',
                'paid'
            )
            ->orderByDesc('paid_at')
            ->limit(100)
            ->get()
            ->map(function ($contribution) {

                return [
                    'id' =>
                        'contribution-' .
                        $contribution->id,

                    'name' =>
                        $contribution->user?->name
                        ?? 'Unknown Member',

                    'type' =>
                        'Contribution',

                    'amount' =>
                        (float) $contribution->amount,

                    'reference' =>
                        $contribution
                            ->mpesaTransaction
                            ?->receipt_number,

                    'description' =>
                        'Group contribution',

                    'status' =>
                        'Success',

                    'created_at' =>
                        optional(
                            $contribution->paid_at
                        )->toISOString(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | LOAN REPAYMENTS
        |--------------------------------------------------------------------------
        |
        | LoanRepayment does not directly contain user_id.
        |
        | The relationship is:
        |
        | LoanRepayment
        |       ↓
        |     Loan
        |       ↓
        |     User
        |
        */

        $loanRepayments = LoanRepayment::with([
            'loan.user',
        ])
            ->whereHas(
                'loan',
                function ($query) use ($groupId) {

                    $query->where(
                        'group_id',
                        $groupId
                    );
                }
            )
            ->orderByDesc('paid_at')
            ->limit(100)
            ->get()
            ->map(function ($repayment) {

                return [
                    'id' =>
                        'repayment-' .
                        $repayment->id,

                    'name' =>
                        $repayment->loan?->user?->name
                        ?? 'Unknown Member',

                    'type' =>
                        'Loan Repayment',

                    'amount' =>
                        (float) $repayment->amount,

                    'reference' =>
                        $repayment->reference,

                    'description' =>
                        'Loan repayment',

                    'status' =>
                        'Success',

                    'created_at' =>
                        optional(
                            $repayment->paid_at
                        )->toISOString(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | EXISTING GENERAL TRANSACTIONS
        |--------------------------------------------------------------------------
        |
        | This keeps compatibility with your existing Transaction table.
        |
        */

        $transactions = Transaction::with('user')
            ->where(
                'group_id',
                $group->id
            )
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($transaction) {

                return [
                    'id' =>
                        'transaction-' .
                        $transaction->id,

                    'name' =>
                        $transaction->user?->name
                        ?? 'Unknown Member',

                    'type' =>
                        $this->formatTransactionType(
                            $transaction->type
                        ),

                    'amount' =>
                        (float) $transaction->amount,

                    'reference' =>
                        $transaction->reference,

                    'description' =>
                        $transaction->description,

                    'status' =>
                        'Success',

                    'created_at' =>
                        optional(
                            $transaction->created_at
                        )->toISOString(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | COMBINE ALL PAYMENT RECORDS
        |--------------------------------------------------------------------------
        */

        $allTransactions = collect()
            ->concat($contributionPayments)
            ->concat($loanRepayments)
            ->concat($transactions)
            ->sortByDesc('created_at')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | MONTHLY GROUP GOAL
        |--------------------------------------------------------------------------
        */

        $groupMonthlyGoal =
            (float) (
                $settings?->monthly_contribution_goal
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | GROUP CONTRIBUTIONS THIS MONTH
        |--------------------------------------------------------------------------
        */

        $thisMonthGroupTotal = Contribution::where(
            'group_id',
            $group->id
        )
            ->where(
                'status',
                'paid'
            )
            ->whereMonth(
                'paid_at',
                now()->month
            )
            ->whereYear(
                'paid_at',
                now()->year
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | GOAL PROGRESS
        |--------------------------------------------------------------------------
        */

        $goalProgress = 0;

        if ($groupMonthlyGoal > 0) {

            $goalProgress = min(
                100,
                round(
                    (
                        $thisMonthGroupTotal /
                        $groupMonthlyGoal
                    ) * 100
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'group' => [
                'id' =>
                    $group->id,

                'name' =>
                    $group->name,

                'unique_code' =>
                    $group->unique_code,
            ],

            /*
            |--------------------------------------------------------------------------
            | MEMBER CONTRIBUTION INFORMATION
            |--------------------------------------------------------------------------
            */

            'contribution' => [

                'minimum' =>
                    $minimumContribution,

                'paid_this_month' =>
                    (float) $paidThisMonth,

                'remaining' =>
                    (float) $remainingThisMonth,

                'total_contributions' =>
                    (float) $totalContributions,

                'due_date' =>
                    $dueDate->toDateString(),

                'days_remaining' =>
                    max(
                        0,
                        $daysRemaining
                    ),

                'status' =>
                    $remainingThisMonth <= 0
                        ? 'Complete'
                        : 'Pending',
            ],

            /*
            |--------------------------------------------------------------------------
            | GROUP MONTHLY GOAL
            |--------------------------------------------------------------------------
            */

            'monthly_goal' => [

                'target' =>
                    $groupMonthlyGoal,

                'current' =>
                    (float) $thisMonthGroupTotal,

                'progress' =>
                    (float) $goalProgress,

                'remaining' =>
                    max(
                        0,
                        $groupMonthlyGoal -
                        $thisMonthGroupTotal
                    ),
            ],

            /*
            |--------------------------------------------------------------------------
            | ALL GROUP PAYMENTS
            |--------------------------------------------------------------------------
            */

            'transactions' =>
                $allTransactions,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT TRANSACTION TYPE
    |--------------------------------------------------------------------------
    */

    private function formatTransactionType(
        ?string $type
    ): string {

        if (!$type) {
            return 'Payment';
        }

        return match (
            strtolower($type)
        ) {

            'contribution' =>
                'Contribution',

            'loan_repayment' =>
                'Loan Repayment',

            'loan repayment' =>
                'Loan Repayment',

            'fine' =>
                'Fine',

            'fee' =>
                'Fee',

            default =>
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $type
                    )
                ),
        };
    }
}