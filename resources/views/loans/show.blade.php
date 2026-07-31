<x-layouts.group
    :group="$group"
    :groups="$groups">


<div class="max-w-6xl mx-auto p-6">


{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">


<div>

<h1 class="text-2xl font-bold text-[#063a2a]">
Loan Details
</h1>


<p class="text-sm text-gray-500">
Complete loan information
</p>


</div>




<span class="px-4 py-2 rounded-full text-sm font-semibold

{{ $loan->status === 'approved'
    ? 'bg-green-100 text-green-700'
    : ($loan->status === 'rejected'
        ? 'bg-red-100 text-red-700'
        : 'bg-yellow-100 text-yellow-700')
}}">

{{ ucfirst($loan->status) }}

</span>


</div>



{{-- SUMMARY CARDS --}}

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">


<div class="bg-white rounded-xl shadow p-5">

<p class="text-xs text-gray-500">
Applicant
</p>

<h2 class="font-semibold text-lg">
{{ $loan->user->name }}
</h2>

<p class="text-sm text-gray-500">
{{ $loan->user->phone_no ?? '-' }}
</p>

</div>




<div class="bg-white rounded-xl shadow p-5">

<p class="text-xs text-gray-500">
Loan Amount
</p>


<h2 class="text-xl font-bold text-emerald-700">

KES {{ number_format($loan->amount) }}

</h2>


</div>




<div class="bg-white rounded-xl shadow p-5">

<p class="text-xs text-gray-500">
Remaining Balance
</p>


<h2 class="text-xl font-bold text-blue-700">

KES {{ number_format($loan->remaining_balance) }}

</h2>


</div>


</div>

<div class="bg-white rounded-xl shadow p-6 my-6">

<h2 class="font-semibold text-lg mb-4">
Make Repayment
</h2>


<form method="POST" action="{{ route('mpesa.stk') }}">

    @csrf

    <input type="hidden" name="payment_type" value="loan_repayment">

    <input type="hidden" name="loan_id" value="{{ $loan->id }}">


    <input 
        type="number"
        name="amount"
        placeholder="Amount to repay"
        class="border rounded-lg px-4 py-3 w-full mb-3"
        max="{{ $loan->remaining_balance }}"
        required
    >


    <button
        class="bg-emerald-600 text-white px-5 py-3 rounded-lg">

        Pay Loan via M-Pesa

    </button>

</form>


</div>

{{-- LOAN INFORMATION --}}

<div class="bg-white rounded-xl shadow p-6 mb-6">


<h2 class="font-semibold text-lg mb-4">
Loan Information
</h2>


<div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">


<div>

<p class="text-gray-500">
Interest Rate
</p>

<p class="font-semibold">
{{ $loan->interest_rate }}%
</p>

</div>



<div>

<p class="text-gray-500">
Duration
</p>

<p class="font-semibold">
{{ $loan->duration_days }} days
</p>

</div>




<div>

<p class="text-gray-500">
Total Payable
</p>

<p class="font-semibold">
KES {{ number_format($loan->total_payable) }}
</p>

</div>



<div>

<p class="text-gray-500">
Due Date
</p>

<p class="font-semibold">

{{ $loan->due_date?->format('d M Y') ?? '-' }}

</p>

</div>


</div>



<div class="mt-5">

<p class="text-gray-500 text-sm">
Reason
</p>

<p class="mt-1">
{{ $loan->reason }}
</p>

</div>


</div>

<div class="bg-white rounded-xl shadow p-6 mt-6">

<h2 class="font-semibold text-lg mb-4">
Approval History
</h2>


@forelse($loan->approvals as $approval)

<div class="border-b py-3">

<div class="flex justify-between">

<div>

<p class="font-medium">
{{ $approval->approver->name }}
</p>


<p class="text-sm text-slate-500">

{{ $approval->decision }}

</p>


</div>


<div class="text-sm text-slate-400">

{{ $approval->approved_at?->format('d M Y H:i') }}

</div>

</div>


@if($approval->comment)

<p class="mt-2 text-sm text-slate-600">

"{{ $approval->comment }}"

</p>

@endif


</div>


@empty

<p class="text-slate-500">
No approvals yet.
</p>

@endforelse


</div>


</x-layouts.group>