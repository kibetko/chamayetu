<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Group;
use App\Models\Loan;
use App\Models\LoanApproval;
use App\Models\LoanRepayment;
use App\Models\User;
use App\Notifications\ChamaNotification;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $groupId = session('active_group_id');

        $group = Group::with('settings')->findOrFail($groupId);

        $groups = auth()->user()->groups;

        $totalContributions = $group->total_contributions;

        $totalLoaned = $group->total_loaned;

        $totalRepayments = LoanRepayment::whereHas('loan', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->sum('amount');

        $totalDisbursed = Loan::where('group_id', $groupId)
            ->whereIn('status', ['approved', 'disbursed', 'overdue'])
            ->sum('amount');

        $available = $totalContributions
            + $totalRepayments
            - $totalDisbursed;

        $myLoans = Loan::with('repayments')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $groupLoans = Loan::with([
            'user',
            'approvals.approver',
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

        $totalContributions = $group->total_contributions;

        $totalLoaned = $group->total_loaned;

        $totalRepayments = LoanRepayment::whereHas('loan', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->sum('amount');

        $availableFunds = max(
            0,
            $totalContributions
            + $totalRepayments
            - $totalLoaned
        );

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

    /*
    |--------------------------------------------------------------------------
    | CALCULATE AVAILABLE GROUP FUNDS
    |--------------------------------------------------------------------------
    */

    $totalContributions = $group->total_contributions;

    $totalLoaned = $group->total_loaned;

    $totalRepayments = LoanRepayment::whereHas('loan', function ($query) use ($group) {
        $query->where('group_id', $group->id);
    })->sum('amount');

    $availableFunds = max(
        0,
        $totalContributions
        + $totalRepayments
        - $totalLoaned
    );

    /*
    |--------------------------------------------------------------------------
    | CHECK AVAILABLE FUNDS
    |--------------------------------------------------------------------------
    */

    if ($request->amount > $availableFunds) {
        return back()
            ->withInput()
            ->withErrors([
                'amount' => 'Maximum loan available is KES ' .
                    number_format($availableFunds),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK USER CONTRIBUTIONS
    |--------------------------------------------------------------------------
    */

    $userContributions = Contribution::where('group_id', $group->id)
        ->where('user_id', auth()->id())
        ->sum('amount');

    if ($userContributions <= 0) {
        return back()
            ->withInput()
            ->withErrors([
                'amount' => 'You must make contributions before applying for a loan.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK PERSONAL LOAN LIMIT
    |--------------------------------------------------------------------------
    */

    $maxLoan = $userContributions *
        $group->settings->maximum_loan_multiplier;

    if ($request->amount > $maxLoan) {
        return back()
            ->withInput()
            ->withErrors([
                'amount' => 'Your maximum loan limit is KES ' .
                    number_format($maxLoan),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK EXISTING ACTIVE LOAN
    |--------------------------------------------------------------------------
    */

    $activeLoan = Loan::where('user_id', auth()->id())
        ->whereIn('status', [
            'pending',
            'approved',
            'disbursed',
            'overdue'
        ])
        ->exists();

    if ($activeLoan) {
        return back()
            ->withErrors([
                'amount' => 'You already have an active loan.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUEST
    |--------------------------------------------------------------------------
    */

    $request->validate([
        'amount' => 'required|numeric|min:1',
        'duration_days' => 'required|integer|min:1|max:' . $maxMonths,
        'reason' => 'required|string',
    ]);

    /*
    |--------------------------------------------------------------------------
    | CALCULATE INTEREST
    |--------------------------------------------------------------------------
    */

    $interestRate = $group->settings->interest_rate ?? 0;

    $months = (int) $request->duration_days;

    $interestAmount =
        $request->amount *
        ($interestRate / 100) *
        ($months / 30);

    $totalPayable = $request->amount + $interestAmount;

    /*
    |--------------------------------------------------------------------------
    | CREATE LOAN
    |--------------------------------------------------------------------------
    */

    $loan = Loan::create([
        'group_id' => $group->id,
        'user_id' => auth()->id(),
        'amount' => $request->amount,
        'total_payable' => $totalPayable,
        'interest_rate' => $interestRate,
        'duration_days' => $months,
        'reason' => $request->reason,
        'status' => 'pending',
    ]);

    /*
    |--------------------------------------------------------------------------
    | NOTIFY COMMITTEE
    |--------------------------------------------------------------------------
    */

    $officials = $group->members()
        ->wherePivotIn('role', [
            'chairperson',
            'secretary',
            'treasurer',
        ])
        ->get();

    foreach ($officials as $official) {
        $official->notify(
            new ChamaNotification(
                'New Loan Request',
                auth()->user()->name .
                ' has requested a loan of KES ' .
                number_format($loan->amount),
                url('/loans/' . $loan->id)
            )
        );
    }

    return redirect()
        ->route('loans.index')
        ->with(
            'success',
            'Loan request submitted successfully.'
        );
}

    /**
     * ✅ CHAIRPERSON OVERRIDE APPROVAL SYSTEM
     */
    public function approve(Request $request, Loan $loan)
{
    $group = $loan->group;

    /*
    |--------------------------------------------------------------------------
    | BORROWER CANNOT APPROVE THEIR OWN LOAN
    |--------------------------------------------------------------------------
    */

    if ($loan->user_id === auth()->id()) {
        return back()->with(
            'error',
            'You cannot approve your own loan.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ANY OTHER GROUP MEMBER CAN APPROVE
    |--------------------------------------------------------------------------
    */

    if (! $group->members()
        ->where('user_id', auth()->id())
        ->exists()) {

        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | PREVENT DUPLICATE DECISION
    |--------------------------------------------------------------------------
    */

    $exists = LoanApproval::where('loan_id', $loan->id)
        ->where('approved_by', auth()->id())
        ->exists();

    if ($exists) {
        return back()->with(
            'error',
            'You have already made a decision on this loan.'
        );
    }

    LoanApproval::create([
        'loan_id' => $loan->id,
        'approved_by' => auth()->id(),
        'decision' => 'approved',
        'comment' => $request->comment,
        'approved_at' => now(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | 3 APPROVALS REQUIRED
    |--------------------------------------------------------------------------
    */

    $approvalCount = $loan->approvals()
        ->where('decision', 'approved')
        ->count();

    if ($approvalCount >= 3) {

        $loan->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $loan->user->notify(
            new ChamaNotification(
                'Loan Approved',
                'Your loan request of KES ' .
                number_format($loan->amount) .
                ' has been approved.',
                url('/loans/' . $loan->id)
            )
        );
    }

    return back()->with(
        'success',
        'Your loan approval has been recorded.'
    );
}


public function reject(Request $request, Loan $loan)
{
    $group = $loan->group;

    /*
    |--------------------------------------------------------------------------
    | BORROWER CANNOT REJECT THEIR OWN LOAN
    |--------------------------------------------------------------------------
    */

    if ($loan->user_id === auth()->id()) {
        return back()->with(
            'error',
            'You cannot reject your own loan.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ANY OTHER GROUP MEMBER CAN REJECT
    |--------------------------------------------------------------------------
    */

    if (! $group->members()
        ->where('user_id', auth()->id())
        ->exists()) {

        abort(403);
    }

    $request->validate([
        'comment' => 'required|string|max:500',
    ]);

    /*
    |--------------------------------------------------------------------------
    | PREVENT DUPLICATE DECISION
    |--------------------------------------------------------------------------
    */

    $exists = LoanApproval::where('loan_id', $loan->id)
        ->where('approved_by', auth()->id())
        ->exists();

    if ($exists) {
        return back()->with(
            'error',
            'You have already made a decision on this loan.'
        );
    }

    LoanApproval::create([
        'loan_id' => $loan->id,
        'approved_by' => auth()->id(),
        'decision' => 'rejected',
        'comment' => $request->comment,
        'approved_at' => now(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | ONE REJECTION REJECTS THE LOAN
    |--------------------------------------------------------------------------
    */

    $loan->update([
        'status' => 'rejected',
    ]);

    $loan->user->notify(
        new ChamaNotification(
            'Loan Rejected',
            'Your loan request of KES ' .
            number_format($loan->amount) .
            ' was rejected.',
            url('/loans/' . $loan->id)
        )
    );

    return back()->with(
        'success',
        'Loan rejected successfully.'
    );
}

    public function disburse(Loan $loan)
{
    $group = Group::findOrFail(
        session('active_group_id')
    );

    /*
    |--------------------------------------------------------------------------
    | ONLY COMMITTEE MEMBERS CAN DISBURSE
    |--------------------------------------------------------------------------
    */

    $isCommitteeMember = $group->members()
        ->where('user_id', auth()->id())
        ->whereIn('role', [
            'chairperson',
            'secretary',
            'treasurer',
        ])
        ->exists();

    if (! $isCommitteeMember) {
        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | MAKE SURE LOAN BELONGS TO ACTIVE GROUP
    |--------------------------------------------------------------------------
    */

    if ($loan->group_id !== $group->id) {
        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | ONLY APPROVED LOANS CAN BE DISBURSED
    |--------------------------------------------------------------------------
    */

    if ($loan->status !== 'approved') {
        return back()->with(
            'error',
            'Only approved loans can be disbursed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DISBURSE LOAN
    |--------------------------------------------------------------------------
    */

    $loan->update([
        'status' => 'disbursed',
        'disbursed_at' => now(),
        'due_date' => now()->addDays($loan->duration_days),
    ]);

    /*
    |--------------------------------------------------------------------------
    | UPDATE GROUP TOTAL LOANED
    |--------------------------------------------------------------------------
    */

    $group->increment(
        'total_loaned',
        $loan->amount
    );

    /*
    |--------------------------------------------------------------------------
    | NOTIFY BORROWER
    |--------------------------------------------------------------------------
    */

    $loan->user->notify(
        new ChamaNotification(
            'Loan Disbursed',
            'Your loan of KES ' .
            number_format($loan->amount) .
            ' has been disbursed.',
            url('/loans/' . $loan->id)
        )
    );

    return back()->with(
        'success',
        'Loan disbursed successfully.'
    );
}

    public function repay(Request $request, Loan $loan)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        LoanRepayment::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'paid_at' => now(),
        ]);
        $loan->user->notify(
            new ChamaNotification(
                'Repayment Received',
                'Your repayment of KES '.
                number_format($request->amount).
                ' has been received.',
                url('/loans/'.$loan->id)
            )
        );

        if ($loan->remaining_balance <= 0) {

            $loan->update([
                'status' => 'completed',
            ]);

            $loan->user->notify(
                new ChamaNotification(
                    'Loan Completed',
                    'Congratulations, your loan has been fully repaid.',
                    url('/loans/'.$loan->id)
                )
            );

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
            'repayments',
        ]);

        return view('loans.show', compact(
            'loan',
            'group',
            'groups'
        ));
    }
}
