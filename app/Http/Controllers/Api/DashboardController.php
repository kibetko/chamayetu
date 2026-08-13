<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHECK GROUP MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        if ($user->groups()->count() === 0) {
            return response()->json([
                'message' => 'You are not a member of any group.',
                'has_group' => false,
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVE GROUP
        |--------------------------------------------------------------------------
        |
        | Mobile does not use Laravel session().
        |
        | For now we use the first group.
        | Later we can add group switching.
        |
        */

        $group = $user->groups()
            ->with([
                'members',
                'contributions',
                'loans',
                'settings',
            ])
            ->first();

        /*
        |--------------------------------------------------------------------------
        | GENERAL STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalContributions = $group->contributions()
            ->where('status', 'paid')
            ->sum('amount');

        $myContributions = $group->contributions()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | LOANS
        |--------------------------------------------------------------------------
        */

        $allLoans = Loan::where('group_id', $group->id)
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue',
                'completed'
            ])
            ->with('repayments')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LOAN STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalLoaned = $allLoans->sum(function ($loan) {
            return (float) $loan->amount;
        });

        $totalPayable = $allLoans->sum(function ($loan) {
            return (float) (
                $loan->total_payable
                ?? $loan->amount
            );
        });

        $totalInterest = max(
            0,
            $totalPayable - $totalLoaned
        );

        $totalRepaid = $allLoans->sum(function ($loan) {

            return $loan->repayments->sum(function ($repayment) {
                return (float) $repayment->amount;
            });

        });

        $outstanding = max(
            0,
            $totalPayable - $totalRepaid
        );

        /*
        |--------------------------------------------------------------------------
        | LOAN COUNTS
        |--------------------------------------------------------------------------
        */

        $activeLoans = $allLoans
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue'
            ])
            ->count();

        $completedLoans = $allLoans
            ->where('status', 'completed')
            ->count();

        $overdueLoans = $allLoans
            ->where('status', 'overdue')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE FUNDS
        |--------------------------------------------------------------------------
        |
        | Keeping the same calculation currently used
        | by your web dashboard.
        |
        */

        $interestEarned = max(
            0,
            $totalRepaid - $totalLoaned
        );

        $totalAvailable =
            $totalContributions + $interestEarned;

        /*
        |--------------------------------------------------------------------------
        | RECOVERY RATE
        |--------------------------------------------------------------------------
        */

        $recoveryRate = 0;

        if ($totalPayable > 0) {

            $recoveryRate =
                ($totalRepaid / $totalPayable) * 100;

            $recoveryRate = min(
                100,
                max(0, $recoveryRate)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHLY CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $monthlyContributions = $group->contributions()
            ->where('status', 'paid')
            ->selectRaw("
                TO_CHAR(
                    DATE_TRUNC('month', created_at),
                    'Mon YYYY'
                ) as month,

                DATE_TRUNC(
                    'month',
                    created_at
                ) as month_date,

                SUM(amount) as total
            ")
            ->groupByRaw("
                DATE_TRUNC(
                    'month',
                    created_at
                )
            ")
            ->orderBy('month_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RECENT CONTRIBUTIONS
        |--------------------------------------------------------------------------
        */

        $recentContributions = $group->contributions()
            ->where('status', 'paid')
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($contribution) {

                return [
                    'type' => 'contribution',
                    'title' => 'Contribution Received',
                    'amount' => (float) $contribution->amount,
                    'user' => $contribution->user?->name,
                    'created_at' => $contribution->created_at,
                ];

            });

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'has_group' => true,

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],

            /*
            |--------------------------------------------------------------------------
            | GROUP
            |--------------------------------------------------------------------------
            */

            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'unique_code' => $group->unique_code,
                'members' => $group->members->count(),
            ],

            /*
            |--------------------------------------------------------------------------
            | SUMMARY
            |--------------------------------------------------------------------------
            */

            'summary' => [

                'total_contributions' =>
                    (float) $totalContributions,

                'my_contributions' =>
                    (float) $myContributions,

                'total_available' =>
                    (float) $totalAvailable,

                'total_loaned' =>
                    (float) $totalLoaned,

                'total_repaid' =>
                    (float) $totalRepaid,

                'total_payable' =>
                    (float) $totalPayable,

                'total_interest' =>
                    (float) $totalInterest,

                'outstanding' =>
                    (float) $outstanding,

            ],

            /*
            |--------------------------------------------------------------------------
            | LOANS
            |--------------------------------------------------------------------------
            */

            'loans' => [

                'active' =>
                    $activeLoans,

                'completed' =>
                    $completedLoans,

                'overdue' =>
                    $overdueLoans,

                'recovery_rate' =>
                    round($recoveryRate, 2),

            ],

            /*
            |--------------------------------------------------------------------------
            | MONTHLY CONTRIBUTIONS
            |--------------------------------------------------------------------------
            */

            'monthly_contributions' =>
                $monthlyContributions->map(function ($item) {

                    return [
                        'month' => $item->month,
                        'total' => (float) $item->total,
                    ];

                })->values(),

            /*
            |--------------------------------------------------------------------------
            | RECENT ACTIVITY
            |--------------------------------------------------------------------------
            */

            'recent_activity' =>
                $recentContributions->values(),

        ]);
    }
}