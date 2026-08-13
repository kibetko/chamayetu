<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\MpesaTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        Log::info('MOBILE PAYMENTS REQUEST', [
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
        | CHECK MEMBERSHIP
        |--------------------------------------------------------------------------
        |
        | Your database may use either:
        |
        | active
        | approved
        |
        | Both are accepted for the mobile application.
        |
        */

        $membership = $user->groups()
            ->where('groups.id', $group->id)
            ->first();

        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to this group.',
                'debug' => [
                    'user_id' => $user->id,
                    'group_id' => (int) $group->id,
                    'status' => null,
                ],
            ], 403);
        }

        $membershipStatus = $membership->pivot->status ?? null;

        /*
        |--------------------------------------------------------------------------
        | ALLOW ACTIVE OR APPROVED
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            strtolower((string) $membershipStatus),
            ['active', 'approved'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Your membership in this group is not active.',
                'debug' => [
                    'user_id' => $user->id,
                    'group_id' => (int) $group->id,
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
            (float) (
                $settings?->minimum_contribution ?? 0
            );

        $dueDay =
            (int) (
                $settings?->contribution_due_day ?? 30
            );

        /*
        |--------------------------------------------------------------------------
        | CURRENT USER CONTRIBUTIONS
        |--------------------------------------------------------------------------
        |
        | These values are still personal to the logged-in member.
        |
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
        | CURRENT USER CONTRIBUTIONS THIS MONTH
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
        | CURRENT USER REMAINING CONTRIBUTION
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
        | GROUP TOTAL CONTRIBUTIONS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | This is the total contribution of EVERY MEMBER
        | in the active group.
        |
        */

        $groupTotalContributions = Contribution::where(
            'group_id',
            $group->id
        )
            ->where(
                'status',
                'paid'
            )
            ->sum('amount');

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
        | TRANSACTION TABLE
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We DO NOT filter by user_id here.
        |
        | This means every member's transactions can appear.
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
                        'transaction-' . $transaction->id,

                    'name' =>
                        $transaction->user?->name
                        ?? 'Unknown',

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
        |
        | IMPORTANT:
        |
        | Again, NO user_id filter.
        |
        | We show M-Pesa payments made by all members
        | of the active group.
        |
        */

        $mpesaTransactions = MpesaTransaction::with('user')
            ->where(
                'group_id',
                $group->id
            )
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($transaction) {

                $paymentType =
                    strtolower(
                        (string) $transaction->payment_type
                    );

                if ($paymentType === 'loan_repayment') {
                    $type = 'Loan Repayment';
                    $description = 'Loan repayment';
                } elseif ($paymentType === 'contribution') {
                    $type = 'Contribution';
                    $description = 'Group contribution';
                } else {
                    $type = ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $paymentType ?: 'Payment'
                        )
                    );

                    $description = 'Group payment';
                }

                return [
                    'id' =>
                        'mpesa-' . $transaction->id,

                    'name' =>
                        $transaction->user?->name
                        ?? 'Unknown',

                    'type' =>
                        $type,

                    'amount' =>
                        (float) $transaction->amount,

                    'reference' =>
                        $transaction->receipt_number,

                    'description' =>
                        $description,

                    'status' =>
                        ucfirst(
                            strtolower(
                                (string) (
                                    $transaction->status
                                    ?? 'pending'
                                )
                            )
                        ),

                    'created_at' =>
                        optional(
                            $transaction->created_at
                        )->toISOString(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | ALSO INCLUDE CONTRIBUTIONS
        |--------------------------------------------------------------------------
        |
        | This is useful because your Contribution table is where
        | completed contribution payments are stored.
        |
        | This ensures that even if a payment does not have a
        | Transaction record, it can still appear in the mobile
        | payment history.
        |
        */

        $contributionTransactions =
            Contribution::with('user')
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
                            ?? 'Unknown',

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
        | COMBINE ALL PAYMENT SOURCES
        |--------------------------------------------------------------------------
        */

        $allTransactions = $transactions
            ->concat($mpesaTransactions)
            ->concat($contributionTransactions)
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
        | GROUP GOAL REMAINING
        |--------------------------------------------------------------------------
        */

        $goalRemaining = max(
            0,
            $groupMonthlyGoal -
            $thisMonthGroupTotal
        );

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

                'id' =>
                    $group->id,

                'name' =>
                    $group->name,

                'unique_code' =>
                    $group->unique_code,

                'membership_status' =>
                    $membershipStatus,
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSONAL CONTRIBUTION
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
            | GROUP CONTRIBUTION SUMMARY
            |--------------------------------------------------------------------------
            */

            'group_contributions' => [

                'total' =>
                    (float) $groupTotalContributions,

                'this_month' =>
                    (float) $thisMonthGroupTotal,
            ],

            /*
            |--------------------------------------------------------------------------
            | MONTHLY GROUP GOAL
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
                    (float) $goalRemaining,
            ],

            /*
            |--------------------------------------------------------------------------
            | GROUP TRANSACTION HISTORY
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