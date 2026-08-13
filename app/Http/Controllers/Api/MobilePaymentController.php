<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\MpesaTransaction;
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
        | VALIDATE GROUP ID
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
        | CHECK GROUP MEMBERSHIP
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | We deliberately check the user's group relationship directly.
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

/*
|--------------------------------------------------------------------------
| MOBILE MEMBERSHIP CHECK
|--------------------------------------------------------------------------
|
| Your existing system currently uses "active" for this member.
| We therefore allow both active and approved memberships.
|
*/

if (!in_array($membershipStatus, ['active', 'approved'], true)) {
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
        | CHECK MEMBERSHIP STATUS
        |--------------------------------------------------------------------------
        |
        | Your web application uses "approved".
        | We therefore still require an approved membership.
        |
        */

        $pivotStatus = $membership->pivot->status ?? null;

        if (
            $pivotStatus !== null &&
            $pivotStatus !== 'approved'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not approved.',
                'debug' => [
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'status' => $pivotStatus,
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
        | USER CONTRIBUTIONS
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
        | THIS MONTH
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

            $dueDate = $nextMonth->day(
                $safeNextDueDay
            );
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
        | TRANSACTIONS TABLE
        |--------------------------------------------------------------------------
        */

        $transactions = Transaction::with('user')
            ->where(
                'group_id',
                $group->id
            )
            ->where(
                'user_id',
                $user->id
            )
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($transaction) {

                return [
                    'id' => (string) $transaction->id,

                    'name' =>
                        $transaction->user?->name
                        ?? 'You',

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
        | M-PESA TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $mpesaTransactions = MpesaTransaction::where(
            'group_id',
            $group->id
        )
            ->where(
                'user_id',
                $user->id
            )
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($transaction) {

                return [
                    'id' =>
                        'mpesa-' . $transaction->id,

                    'name' =>
                        'You',

                    'type' =>
                        $transaction->payment_type ===
                        'loan_repayment'
                            ? 'Loan Repayment'
                            : 'Contribution',

                    'amount' =>
                        (float) $transaction->amount,

                    'reference' =>
                        $transaction->receipt_number,

                    'description' =>
                        $transaction->payment_type ===
                        'loan_repayment'
                            ? 'Loan repayment'
                            : 'Group contribution',

                    'status' =>
                        ucfirst(
                            $transaction->status
                        ),

                    'created_at' =>
                        optional(
                            $transaction->created_at
                        )->toISOString(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | COMBINE TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $allTransactions = $transactions
            ->concat($mpesaTransactions)
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

        return match (strtolower($type)) {

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