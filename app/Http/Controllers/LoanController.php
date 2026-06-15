<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Group;
use App\Models\Contribution;
use App\Models\LoanApproval;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;

class LoanController extends Controller
{
   public function index()
{
    $groupId = session('active_group_id');

    $group = Group::with('settings')
        ->findOrFail($groupId);

    $groups = auth()->user()->groups;

    $totalContributions = Contribution::where(
        'group_id',
        $groupId
    )->sum('amount');

    $totalLoaned = Loan::where(
        'group_id',
        $groupId
    )
    ->whereIn('status', [
        'approved',
        'disbursed',
        'overdue'
    ])
    ->sum('amount');

    $available = $totalContributions - $totalLoaned;

    $myLoans = Loan::with('repayments')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    $groupLoans = Loan::with([
        'user',
        'approvals'
    ])
    ->where('group_id', $groupId)
    ->latest()
    ->get();

    return view(
        'loans.index',
        compact(
            'group',
            'groups',
            'totalContributions',
            'totalLoaned',
            'available',
            'myLoans',
            'groupLoans'
        )
    );
}

    public function apply()
{
    $group = Group::with('settings')
        ->findOrFail(
            session('active_group_id')
        );

    $groups = auth()->user()->groups;

    return view(
        'loans.apply',
        compact(
            'group',
            'groups'
        )
    );
}

    public function store(Request $request)
{
    $group = Group::with('settings')
        ->findOrFail(
            session('active_group_id')
        );

    $maxMonths =
        $group->settings->repayment_period_days ?? 12;

    $request->validate([
        'amount' => 'required|numeric|min:1',
        'duration_days' => [
            'required',
            'integer',
            'min:1',
            'max:' . $maxMonths
        ],
        'reason' => 'required|string'
    ]);

    $interestRate =
        $group->settings->interest_rate ?? 0;

    /*
    |--------------------------------------------------------------------------
    | Duration selected from slider
    | Example:
    | 1 = 1 month
    | 2 = 2 months
    | 10 = 10 months
    |--------------------------------------------------------------------------
    */

    $months = (int) $request->duration_days;

    /*
    |--------------------------------------------------------------------------
    | Interest Calculation
    |--------------------------------------------------------------------------
    |
    | Amount = 10,000
    | Interest = 10%
    |
    | Month 1 = 1,000
    | Month 2 = 2,000
    | Month 3 = 3,000
    |
    */

    $interestAmount =
    $request->amount *
    ($interestRate / 100) *
    ($request->duration_days / 30);

$totalPayable =
    $request->amount +
    $interestAmount;

    Loan::create([
        'group_id'       => $group->id,
        'user_id'        => auth()->id(),
        'amount'         => $request->amount,
        'total_payable'  => $totalPayable,
        'interest_rate'  => $interestRate,
        'duration_days'  => $months,
        'reason'         => $request->reason,
        'status'         => 'pending',
    ]);

    return redirect()
        ->route('loans.index')
        ->with(
            'success',
            'Loan request submitted successfully.'
        );
}

    public function approve(Loan $loan)
    {
        if ($loan->user_id == auth()->id()) {
            return back();
        }

        $exists = LoanApproval::where(
    'loan_id',
    $loan->id
)
->where(
    'approved_by',
    auth()->id()
)
->exists();

        if ($exists) {
            return back();
        }

        LoanApproval::create([
    'loan_id' => $loan->id,
    'approved_by' => auth()->id(),
    'decision' => 'approved',
    'approved_at' => now(),
]);

        if ($loan->approval_count >= 3) {

            $loan->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);
        }

        return back();
    }

    public function disburse(Loan $loan)
    {
        $group = Group::findOrFail(
            session('active_group_id')
        );

        if (!$group->isChairperson()) {
            abort(403);
        }

        if ($loan->status !== 'approved') {
            return back();
        }

        $loan->update([
            'status' => 'disbursed',
            'disbursed_at' => now(),
            'due_date' => now()
                ->addDays(
                    $loan->duration_days
                )
        ]);

        return back()
            ->with(
                'success',
                'Loan disbursed successfully'
            );
    }

    public function repay(
        Request $request,
        Loan $loan
    )
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        LoanRepayment::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'paid_at' => now()
        ]);

        if ($loan->remaining_balance <= 0) {

            $loan->update([
                'status' => 'completed'
            ]);
        }

        return back();
    }
}