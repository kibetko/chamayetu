<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | CHECK GROUP MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        if ($user->groups()->count() === 0) {
            return view('groups.no-group');
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVE GROUP
        |--------------------------------------------------------------------------
        */

        $activeGroupId = session('active_group_id');

        if (! $activeGroupId) {

            $activeGroupId = $user
                ->groups()
                ->first()
                ->id;

            session([
                'active_group_id' => $activeGroupId,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD GROUP
        |--------------------------------------------------------------------------
        */

        $group = $user->groups()
            ->with([
                'members',
                'contributions',
                'loans',
                'settings',
                'joinRequests.user',
            ])
            ->where('groups.id', $activeGroupId)
            ->first();

        if (! $group) {

            session()->forget('active_group_id');

            return redirect()
                ->route('groups.index')
                ->withErrors([
                    'group' => 'Invalid active group.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GENERAL STATISTICS
        |--------------------------------------------------------------------------
        */

        $stats = [

    'members' => $group->members->count(),

    // Total paid contributions by all members
    'contributions' => $group->contributions()
        ->where('status', 'paid')
        ->sum('amount'),

    // Logged-in user's contributions
    'my_contributions' => $group->contributions()
        ->where('user_id', $user->id)
        ->where('status', 'paid')
        ->sum('amount'),

    'active_loans' => $group->loans()
        ->whereIn('status', [
            'approved',
            'disbursed',
            'overdue'
        ])
        ->count(),

    'pending_requests' => method_exists($group, 'joinRequests')
        ? $group->joinRequests()
            ->where('status', 'pending')
            ->count()
        : 0,
];

        /*
        |--------------------------------------------------------------------------
        | LOAD ALL RELEVANT LOANS
        |--------------------------------------------------------------------------
        |
        | We load repayments together with the loans so that we can calculate:
        |
        | Principal
        | Interest
        | Total payable
        | Total repaid
        | Outstanding
        |
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

        // Original principal that was borrowed
        $totalLoaned = $allLoans->sum(function ($loan) {
            return (float) $loan->amount;
        });

        // Total amount that borrowers are expected to repay
        // including interest.
        $totalPayable = $allLoans->sum(function ($loan) {

            return (float) (
                $loan->total_payable
                ?? $loan->amount
            );

        });

        // Interest = total payable - principal
        $totalInterest = max(
            0,
            $totalPayable - $totalLoaned
        );

        // Total repayments made against all loans
        $totalRepaid = $allLoans->sum(function ($loan) {

            return $loan->repayments->sum(function ($repayment) {
                return (float) $repayment->amount;
            });

        });

        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING
        |--------------------------------------------------------------------------
        |
        | Outstanding must be calculated against total payable,
        | NOT against the original loan amount.
        |
        | max(0, ...) prevents negative balances.
        |
        */

        $outstanding = max(
            0,
            $totalPayable - $totalRepaid
        );

        /*
        |--------------------------------------------------------------------------
        | ACTIVE / COMPLETED / OVERDUE COUNTS
        |--------------------------------------------------------------------------
        */

        $activeLoansCount = $allLoans
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue'
            ])
            ->count();

        $completedLoansCount = $allLoans
            ->where('status', 'completed')
            ->count();

        $overdueLoansCount = $allLoans
            ->where('status', 'overdue')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | LOAN STATISTICS ARRAY
        |--------------------------------------------------------------------------
        */

        $loanStats = [

            'total_loaned' => $totalLoaned,

            'total_interest' => $totalInterest,

            'total_payable' => $totalPayable,

            'total_repaid' => $totalRepaid,

            'outstanding' => $outstanding,

            'active_loans' => $activeLoansCount,

            'completed_loans' => $completedLoansCount,

            'overdue_loans' => $overdueLoansCount,

        ];

        /*
        |--------------------------------------------------------------------------
        | RECOVERY RATE
        |--------------------------------------------------------------------------
        |
        | Recovery is based on total payable, including interest.
        |
        */

        $recoveryRate = 0;

        if ($totalPayable > 0) {

            $recoveryRate = (
                $totalRepaid / $totalPayable
            ) * 100;

            // Never display more than 100%
            $recoveryRate = min(
                100,
                max(0, $recoveryRate)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MONTHLY LOANS
        |--------------------------------------------------------------------------
        */

        $monthlyLoans = Loan::where(
            'group_id',
            $group->id
        )
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue',
                'completed'
            ])
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

        $loanStats['interest_earned'] = max(
    0,
    $loanStats['total_repaid'] - $loanStats['total_loaned']
);

$stats['total_available'] =
    $stats['contributions'] + $loanStats['interest_earned'];
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
        | TOP BORROWERS
        |--------------------------------------------------------------------------
        */

        $topBorrowers = Loan::with('user')
            ->where(
                'group_id',
                $group->id
            )
            ->whereIn('status', [
                'approved',
                'disbursed',
                'overdue',
                'completed'
            ])
            ->selectRaw("
                user_id,
                SUM(amount) as total_borrowed
            ")
            ->groupBy('user_id')
            ->orderByDesc('total_borrowed')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TOP CONTRIBUTORS
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| DAILY CONTRIBUTIONS - LAST 2 MONTHS
|--------------------------------------------------------------------------
*/

$dailyContributions = $group->contributions()
    ->where('status', 'paid')
    ->where('created_at', '>=', now()->subMonths(2))
    ->selectRaw("
        DATE(created_at) as contribution_date,
        SUM(amount) as total
    ")
    ->groupByRaw("DATE(created_at)")
    ->orderBy('contribution_date')
    ->get();

        $topContributors = $group->contributions()
            ->where('status', 'paid')
            ->selectRaw("
                user_id,
                SUM(amount) as total_contributed
            ")
            ->groupBy('user_id')
            ->orderByDesc('total_contributed')
            ->take(6)
            ->get()
            ->load('user');

        /*
        |--------------------------------------------------------------------------
        | PENDING REQUESTS
        |--------------------------------------------------------------------------
        */

        $pendingRequests = $group
            ->joinRequests()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ONLINE MEMBERS
        |--------------------------------------------------------------------------
        */

        $onlineUserIds = $this->getOnlineUsers();

        $onlineMembers = $group->members()
            ->whereIn(
                'users.id',
                $onlineUserIds
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', [

            'group' => $group,

            'groups' => $user->groups,

            'stats' => $stats,

            'loanStats' => $loanStats,

            'monthlyLoans' => $monthlyLoans,

            'topBorrowers' => $topBorrowers,

            'recoveryRate' => $recoveryRate,

            'pendingRequests' => $pendingRequests,

            'onlineMembers' => $onlineMembers,

            'monthlyContributions' => $monthlyContributions,

            'topContributors' => $topContributors,

            'dailyContributions' => $dailyContributions,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ONLINE USERS
    |--------------------------------------------------------------------------
    */

    private function getOnlineUsers()
    {
        $users = [];

        foreach (
            \App\Models\User::pluck('id') as $id
        ) {

            if (
                Cache::has(
                    'online-user-' . $id
                )
            ) {

                $users[] = $id;
            }
        }

        return $users;
    }
}

