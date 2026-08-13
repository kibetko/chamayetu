<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\Loan;
use App\Models\LoanRepayment;
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

        $groupId = $request->group_id;

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        if (!$groupId) {
            return response()->json([
                'success' => false,
                'message' => 'Group ID is required.',
            ], 422);
        }

        $group = Group::find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY USER BELONGS TO GROUP
        |--------------------------------------------------------------------------
        */

        $belongsToGroup = $user->groups()
            ->where('groups.id', $groupId)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$belongsToGroup) {
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to this group.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings = $group->settings;

        $minimumContribution =
            $settings?->minimum_contribution ?? 0;

        $dueDay =
            $settings?->contribution_due_day ?? 30;

        /*
        |--------------------------------------------------------------------------
        | USER CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $totalContributions = Contribution::where(
            'group_id',
            $groupId
        )
            ->where(
                'user_id',
                $user->id
            )
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | THIS MONTH'S CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $paidThisMonth = Contribution::where(
            'group_id',
            $groupId
        )
            ->where(
                'user_id',
                $user->id
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

        $dueDate = now()
            ->copy()
            ->day(
                min(
                    $dueDay,
                    now()->daysInMonth
                )
            );

        if ($dueDate->isPast()) {
            $dueDate->addMonth();
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
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        |
        | We use your existing Transaction table.
        |
        */

        $transactions = Transaction::with('user')
            ->where(
                'group_id',
                $groupId
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
                    'id' => $transaction->id,

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
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | IF TRANSACTION TABLE DOES NOT CONTAIN EVERYTHING
        |--------------------------------------------------------------------------
        |
        | Also include M-Pesa transactions that belong to the user.
        |
        */

        $mpesaTransactions = MpesaTransaction::where(
            'group_id',
            $groupId
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
                        $transaction->user?->name
                        ?? 'You',

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
            })
            ->values();

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
        | MONTHLY GOAL
        |--------------------------------------------------------------------------
        */

        $groupMonthlyGoal =
            $settings?->monthly_contribution_goal
            ?? 0;

        $thisMonthGroupTotal = Contribution::where(
            'group_id',
            $groupId
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

        $goalProgress = 0;

        if ($groupMonthlyGoal > 0) {
            $goalProgress = min(
                100,
                round(
                    ($thisMonthGroupTotal /
                        $groupMonthlyGoal) * 100
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
                'id' => $group->id,
                'name' => $group->name,
                'unique_code' => $group->unique_code,
            ],

            'contribution' => [

                'minimum' =>
                    (float) $minimumContribution,

                'paid_this_month' =>
                    (float) $paidThisMonth,

                'remaining' =>
                    (float) $remainingThisMonth,

                'total_contributions' =>
                    (float) $totalContributions,

                'due_date' =>
                    $dueDate->toDateString(),

                'days_remaining' =>
                    max(0, $daysRemaining),

                'status' =>
                    $remainingThisMonth <= 0
                        ? 'Complete'
                        : 'Pending',
            ],

            'monthly_goal' => [

                'target' =>
                    (float) $groupMonthlyGoal,

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
    | TRANSACTION TYPE FORMATTER
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