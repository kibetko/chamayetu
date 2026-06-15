<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->groups()->count() === 0) {

            return view('groups.no-group');
        }

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
                    'group' => 'Invalid active group.',
                ]);
        }

        $stats = [

            'members' => $group->members->count(),

            'contributions' => $group->contributions->sum('amount'),

            'active_loans' => $group->loans()
                ->where('status', 'approved')
                ->count(),

            'pending_requests' => method_exists($group, 'joinRequests')
                    ? $group->joinRequests()
                        ->where('status', 'pending')
                        ->count()
                    : 0,
        ];
        $loanStats = [

            'total_loaned' => Loan::where(
                'group_id',
                $group->id
            )->sum('amount'),

            'active_loans' => Loan::where(
                'group_id',
                $group->id
            )
                ->whereIn('status', [
                    'approved',
                    'overdue',
                ])
                ->count(),

            'completed_loans' => Loan::where(
                'group_id',
                $group->id
            )
                ->where('status', 'completed')
                ->count(),

            'overdue_loans' => Loan::where(
                'group_id',
                $group->id
            )
                ->where('status', 'overdue')
                ->count(),
        ];
        $loanStats['total_repaid'] = LoanRepayment::whereHas(
            'loan',
            function ($query) use ($group) {

                $query->where(
                    'group_id',
                    $group->id
                );

            }
        )->sum('amount');

        $loanStats['outstanding'] =
            $loanStats['total_loaned']
            - $loanStats['total_repaid'];

        $monthlyLoans = Loan::where(
            'group_id',
            $group->id
        )
            ->whereNotNull('created_at')
            ->selectRaw("
    TO_CHAR(created_at, 'Mon YYYY') as month,
    DATE_TRUNC('month', created_at) as month_date,
    SUM(amount) as total
")
            ->groupBy('month', 'month_date')
            ->orderBy('month_date')
            ->get();

        $topBorrowers = Loan::with('user')
            ->where('group_id', $group->id)
            ->selectRaw('
        user_id,
        SUM(amount) as total_borrowed
    ')
            ->groupBy('user_id')
            ->orderByDesc('total_borrowed')
            ->take(5)
            ->get();

        $recoveryRate = 0;

        if ($loanStats['total_loaned'] > 0) {

            $recoveryRate =
                (
                    $loanStats['total_repaid']
                    /
                    $loanStats['total_loaned']
                ) * 100;
        }

        $pendingRequests = $group
            ->joinRequests()
            ->with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('dashboard', [
            'group' => $group,
            'groups' => $user->groups,
            'stats' => $stats,
            'loanStats' => $loanStats,
            'monthlyLoans' => $monthlyLoans,
            'topBorrowers' => $topBorrowers,
            'recoveryRate' => $recoveryRate,
            'pendingRequests' => $pendingRequests,
        ]);
    }
}
