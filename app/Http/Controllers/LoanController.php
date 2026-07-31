<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Group;
use App\Models\Contribution;
use App\Models\LoanApproval;
use App\Models\LoanRepayment;
use App\Notifications\LoanSubmittedNotification;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
{
    $groupId = session('active_group_id');

    $group = Group::with('settings')->findOrFail($groupId);

    $groups = auth()->user()->groups;

    $totalContributions = Contribution::where('group_id', $groupId)->sum('amount');

    $totalLoaned = Loan::where('group_id', $groupId)
        ->whereIn('status', ['approved', 'disbursed', 'overdue'])
        ->sum('amount');
        

    $available = $totalContributions - $totalLoaned;

    $myLoans = Loan::with('repayments')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    $groupLoans = Loan::with([
        'user',
        'approvals.approver'
    ])
    ->where('group_id', $groupId)
    ->latest()
    ->get();

    // ✅ NEW: check if current user is official
    $isOfficial = $group->members()
        ->where('user_id', auth()->id())
        ->whereIn('role', ['chairperson', 'secretary', 'treasurer'])
        ->exists();

    return view('loans.index', compact(
        'group',
        'groups',
        'totalContributions',
        'totalLoaned',
        'available',
        'myLoans',
        'groupLoans',
        'isOfficial'
    ));
}

    public function apply()
    {
        $group = Group::with('settings')
            ->findOrFail(session('active_group_id'));

        $groups = auth()->user()->groups;

        $totalContributions = Contribution::where('group_id', $group->id)->sum('amount');

        $totalLoaned = Loan::where('group_id', $group->id)
            ->whereIn('status', ['approved', 'disbursed', 'overdue'])
            ->sum('amount');

        $availableFunds = $totalContributions - $totalLoaned;

        return view('loans.apply', compact(
            'group',
            'groups',
            'availableFunds'
        ));
    }

    public function store(Request $request)
    {
        $group = Group::with('settings')
            ->findOrFail(session('active_group_id'));

        $maxMonths = $group->settings->repayment_period_days ?? 12;

        $totalContributions = Contribution::where('group_id', $group->id)->sum('amount');

        $totalLoaned = Loan::where('group_id', $group->id)
            ->whereIn('status', ['approved', 'disbursed', 'overdue'])
            ->sum('amount');

        $availableFunds = $totalContributions - $totalLoaned;

        if ($request->amount > $availableFunds) {
            return back()->withInput()->withErrors([
                'amount' => 'Maximum loan available is KES ' . number_format($availableFunds)
            ]);
        }

        $userContributions = Contribution::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->sum('amount');

        if ($userContributions <= 0) {
            return back()->withInput()->withErrors([
                'amount' => 'You must make contributions before applying for a loan.'
            ]);
        }

        $maxLoan = $userContributions * $group->settings->maximum_loan_multiplier;

        if ($request->amount > $maxLoan) {
            return back()->withInput()->withErrors([
                'amount' => 'Your maximum loan limit is KES ' . number_format($maxLoan)
            ]);
        }

        $activeLoan = Loan::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'disbursed', 'overdue'])
            ->exists();

        if ($activeLoan) {
            return back()->withErrors([
                'amount' => 'You already have an active loan.'
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1|max:' . $maxMonths,
            'reason' => 'required|string'
        ]);

        $interestRate = $group->settings->interest_rate ?? 0;

        $months = (int) $request->duration_days;

        $interestAmount =
            $request->amount *
            ($interestRate / 100) *
            ($months / 30);

        $totalPayable = $request->amount + $interestAmount;

        $loan = Loan::create([
    'group_id' => $group->id,
    'user_id' => auth()->id(),
    'amount' => $request->amount,
    'total_payable' => $totalPayable,
    'interest_rate' => $interestRate,
    'duration_days' => $months,
    'reason' => $request->reason,
    'status' => 'pending'
]);

$officials = $group->members()
    ->wherePivotIn('role', [
        'chairperson',
        'secretary',
        'treasurer'
    ])
    ->get();

foreach ($officials as $official) {
    $official->notify(
        new LoanSubmittedNotification($loan)
    );
}

        return redirect()
            ->route('loans.index')
            ->with('success', 'Loan request submitted successfully.');
    }

    /**
     * ✅ CHAIRPERSON OVERRIDE APPROVAL SYSTEM
     */
    public function approve(Request $request, Loan $loan)
    {
        $group = $loan->group;

        if (!$group->isLeader()) {
            abort(403);
        }

        $userId = auth()->id();

        // prevent duplicate approval
        $exists = LoanApproval::where('loan_id', $loan->id)
            ->where('approved_by', $userId)
            ->exists();

        if ($exists) {
            return back();
        }

        $isChairperson = $group->isChairperson($userId);

        LoanApproval::create([
            'loan_id' => $loan->id,
            'approved_by' => $userId,
            'decision' => 'approved',
            'comment' => $request->comment,
            'approved_at' => now(),
        ]);

        /**
         * ✅ CHAIRPERSON OVERRIDE LOGIC
         * - If chairperson approves → immediately approve loan
         * - Otherwise require multiple approvals (your threshold)
         */
        if ($isChairperson) {

            $loan->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

        } else {

            // fallback rule (you can adjust threshold later)
            if ($loan->approvals()->count() >= 3) {
                $loan->update([
                    'status' => 'approved',
                    'approved_at' => now()
                ]);
            }
        }

        return back();
    }

    public function reject(Request $request, Loan $loan)
{
    $group = $loan->group;


    if (!$group->isLeader()) {
        abort(403);
    }


    $request->validate([
        'comment' => 'required|string|max:500'
    ]);


    $userId = auth()->id();


    // prevent duplicate decision
    $exists = LoanApproval::where('loan_id', $loan->id)
        ->where('approved_by', $userId)
        ->exists();


    if ($exists) {
        return back()->with('error','You already made a decision on this loan.');
    }


    LoanApproval::create([
        'loan_id' => $loan->id,
        'approved_by' => $userId,
        'decision' => 'rejected',
        'comment' => $request->comment,
        'approved_at' => now()
    ]);


    $loan->update([
        'status' => 'rejected'
    ]);


    return back()->with(
        'success',
        'Loan rejected successfully.'
    );
}


    public function disburse(Loan $loan)
    {
        $group = Group::findOrFail(session('active_group_id'));

        if (!$group->isChairperson()) {
            abort(403);
        }

        if ($loan->status !== 'approved') {
            return back();
        }

        $loan->update([
            'status' => 'disbursed',
            'disbursed_at' => now(),
            'due_date' => now()->addDays($loan->duration_days)
        ]);

        return back()->with('success', 'Loan disbursed successfully');
    }

    public function repay(Request $request, Loan $loan)
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

    public function show(Loan $loan)
{
    $group = $loan->group;

    $groups = auth()->user()->groups;


    $loan->load([
        'user',
        'approvals.approver',
        'repayments'
    ]);


    return view('loans.show', compact(
        'loan',
        'group',
        'groups'
    ));
}

    
}