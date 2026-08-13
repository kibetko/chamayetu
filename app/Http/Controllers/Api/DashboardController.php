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
        | LOAD ALL USER GROUPS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | We deliberately DO NOT use ->first().
        |
        | The user can belong to multiple groups.
        |
        */

        $groups = $user->groups()
            ->with([
                'members',
                'settings',
            ])
            ->wherePivot('status', 'active')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CHECK GROUP MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        if ($groups->isEmpty()) {

            return response()->json([
                'has_group' => false,

                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],

                'groups' => [],

                'message' => 'You are not a member of any group.',
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD DASHBOARD FOR EACH GROUP
        |--------------------------------------------------------------------------
        */

        $groupData = $groups->map(function ($group) use ($user) {

            /*
            |--------------------------------------------------------------------------
            | MEMBER INFORMATION
            |--------------------------------------------------------------------------
            */

            $membership = $group->members
                ->firstWhere('id', $user->id);

            $role = $membership?->pivot?->role;

            /*
            |--------------------------------------------------------------------------
            | CONTRIBUTIONS
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
                    'completed',
                ])
                ->with('repayments')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | TOTAL LOANED
            |--------------------------------------------------------------------------
            */

            $totalLoaned = $allLoans->sum(function ($loan) {

                return (float) $loan->amount;

            });

            /*
            |--------------------------------------------------------------------------
            | TOTAL PAYABLE
            |--------------------------------------------------------------------------
            */

            $totalPayable = $allLoans->sum(function ($loan) {

                return (float) (
                    $loan->total_payable
                    ?? $loan->amount
                );

            });

            /*
            |--------------------------------------------------------------------------
            | INTEREST
            |--------------------------------------------------------------------------
            */

            $totalInterest = max(
                0,
                $totalPayable - $totalLoaned
            );

            /*
            |--------------------------------------------------------------------------
            | TOTAL REPAYMENTS
            |--------------------------------------------------------------------------
            */

            $totalRepaid = $allLoans->sum(function ($loan) {

                return $loan->repayments->sum(
                    function ($repayment) {

                        return (float) $repayment->amount;

                    }
                );

            });

            /*
            |--------------------------------------------------------------------------
            | OUTSTANDING
            |--------------------------------------------------------------------------
            */

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
                    'overdue',
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
            | INTEREST EARNED
            |--------------------------------------------------------------------------
            */

            $interestEarned = max(
                0,
                $totalRepaid - $totalLoaned
            );

            /*
            |--------------------------------------------------------------------------
            | AVAILABLE FUNDS
            |--------------------------------------------------------------------------
            */

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
            | RECENT ACTIVITY
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

                        'title' =>
                            'Contribution Received',

                        'amount' =>
                            (float) $contribution->amount,

                        'user' =>
                            $contribution->user?->name,

                        'created_at' =>
                            $contribution->created_at,
                    ];

                });

            /*
            |--------------------------------------------------------------------------
            | RETURN GROUP DATA
            |--------------------------------------------------------------------------
            */

            return [

                'id' =>
                    $group->id,

                'name' =>
                    $group->name,

                'unique_code' =>
                    $group->unique_code,

                'role' =>
                    $role,

                'members' =>
                    $group->members->count(),

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

                'monthly_contributions' =>
                    $monthlyContributions
                        ->map(function ($item) {

                            return [

                                'month' =>
                                    $item->month,

                                'total' =>
                                    (float) $item->total,

                            ];

                        })
                        ->values(),

                'recent_activity' =>
                    $recentContributions
                        ->values(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'has_group' => true,

            'user' => [

                'id' =>
                    $user->id,

                'name' =>
                    $user->name,

                'email' =>
                    $user->email,
            ],

            /*
            |--------------------------------------------------------------------------
            | ALL GROUPS
            |--------------------------------------------------------------------------
            */

            'groups' =>
                $groupData->values(),

            /*
            |--------------------------------------------------------------------------
            | GROUP COUNT
            |--------------------------------------------------------------------------
            */

            'groups_count' =>
                $groupData->count(),

        ]);
    }
}