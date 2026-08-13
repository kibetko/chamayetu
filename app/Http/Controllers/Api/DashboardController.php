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
        | NO GROUPS
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

                'groups_count' => 0,

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
            | CONTRIBUTION CHART
            |--------------------------------------------------------------------------
            |
            | Get paid contributions from the previous 2 months.
            |
            | Contributions made on the same date are combined into
            | one data point.
            |
            */

            $chartStartDate = now()
                ->subMonths(2)
                ->startOfDay();

            $contributionChart = $group->contributions()
                ->where('status', 'paid')
                ->where('created_at', '>=', $chartStartDate)
                ->selectRaw("
                    DATE(created_at) as contribution_date,
                    SUM(amount) as total
                ")
                ->groupByRaw("DATE(created_at)")
                ->orderBy('contribution_date')
                ->get()
                ->map(function ($item) {

                    return [
                        'date' => $item->contribution_date,
                        'amount' => (float) $item->total,
                    ];

                })
                ->values();

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
            | TOTAL INTEREST
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
            | RECENT ACTIVITY
            |--------------------------------------------------------------------------
            */
/*
|--------------------------------------------------------------------------
| RECENT ACTIVITY
|--------------------------------------------------------------------------
|
| Combine contributions, loans and repayments into one activity feed.
|
*/

$activities = collect();

/*
|--------------------------------------------------------------------------
| CONTRIBUTIONS
|--------------------------------------------------------------------------
*/

$contributionActivities = $group->contributions()
    ->where('status', 'paid')
    ->with('user')
    ->latest()
    ->get()
    ->map(function ($contribution) {

        return [
            'type' => 'contribution',

            'title' => 'Contribution Received',

            'description' =>
                ($contribution->user?->name ?? 'A member')
                . ' made a contribution',

            'amount' =>
                (float) $contribution->amount,

            'user' =>
                $contribution->user?->name,

            'created_at' =>
                $contribution->created_at,
        ];

    });

$activities = $activities->merge($contributionActivities);


/*
|--------------------------------------------------------------------------
| LOANS
|--------------------------------------------------------------------------
*/

$loanActivities = Loan::where('group_id', $group->id)
    ->with([
        'user',
        'repayments',
    ])
    ->get()
    ->flatMap(function ($loan) {

        $loanActivities = collect();

        $userName = $loan->user?->name ?? 'A member';

        /*
        |--------------------------------------------------------------------------
        | LOAN APPLICATION
        |--------------------------------------------------------------------------
        */

        $loanActivities->push([

            'type' => 'loan_applied',

            'title' => 'Loan Application',

            'description' =>
                $userName . ' applied for a loan',

            'amount' =>
                (float) $loan->amount,

            'user' =>
                $userName,

            'created_at' =>
                $loan->created_at,

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAN APPROVED
        |--------------------------------------------------------------------------
        |
        | Only add this if the loan actually has an approval timestamp.
        |
        */

        if (!empty($loan->approved_at)) {

            $loanActivities->push([

                'type' => 'loan_approved',

                'title' => 'Loan Approved',

                'description' =>
                    $userName . '\'s loan was approved',

                'amount' =>
                    (float) $loan->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $loan->approved_at,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN DISBURSED
        |--------------------------------------------------------------------------
        */

        if (!empty($loan->disbursed_at)) {

            $loanActivities->push([

                'type' => 'loan_disbursed',

                'title' => 'Loan Disbursed',

                'description' =>
                    'Loan disbursed to ' . $userName,

                'amount' =>
                    (float) $loan->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $loan->disbursed_at,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN REJECTED
        |--------------------------------------------------------------------------
        */

        if (
            $loan->status === 'rejected' &&
            !empty($loan->updated_at)
        ) {

            $loanActivities->push([

                'type' => 'loan_rejected',

                'title' => 'Loan Rejected',

                'description' =>
                    $userName . '\'s loan was rejected',

                'amount' =>
                    (float) $loan->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $loan->updated_at,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN OVERDUE
        |--------------------------------------------------------------------------
        */

        if (
            $loan->status === 'overdue' &&
            !empty($loan->updated_at)
        ) {

            $loanActivities->push([

                'type' => 'loan_overdue',

                'title' => 'Loan Overdue',

                'description' =>
                    $userName . '\'s loan is overdue',

                'amount' =>
                    (float) $loan->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $loan->updated_at,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN COMPLETED
        |--------------------------------------------------------------------------
        */

        if (
            $loan->status === 'completed' &&
            !empty($loan->updated_at)
        ) {

            $loanActivities->push([

                'type' => 'loan_completed',

                'title' => 'Loan Completed',

                'description' =>
                    $userName . '\'s loan was completed',

                'amount' =>
                    (float) $loan->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $loan->updated_at,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | REPAYMENTS
        |--------------------------------------------------------------------------
        */

        foreach ($loan->repayments as $repayment) {

            $loanActivities->push([

                'type' => 'loan_repayment',

                'title' => 'Loan Repayment',

                'description' =>
                    $userName . ' made a loan repayment',

                'amount' =>
                    (float) $repayment->amount,

                'user' =>
                    $userName,

                'created_at' =>
                    $repayment->created_at,

            ]);

        }


        return $loanActivities;

    });

$activities = $activities->merge($loanActivities);


/*
|--------------------------------------------------------------------------
| SORT ALL ACTIVITIES
|--------------------------------------------------------------------------
*/

$recentActivities = $activities
    ->sortByDesc(function ($activity) {

        return $activity['created_at'];

    })
    ->take(10)
    ->values();

           
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
                | CONTRIBUTION CHART
                |--------------------------------------------------------------------------
                */

                'contribution_chart' =>
                    $contributionChart,

                /*
                |--------------------------------------------------------------------------
                | RECENT ACTIVITY
                |--------------------------------------------------------------------------
                */

                'recent_activity' =>
    $recentActivities,
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