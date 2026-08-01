<x-layouts.group
    :group="$group"
    :groups="$groups">


<div class="max-w-6xl mx-auto p-3 sm:p-5 lg:p-8">



{{-- HEADER --}}

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">


<div>

<h1 class="text-2xl sm:text-3xl font-bold text-[#063a2a]">

Loan Details

</h1>


<p class="text-sm text-gray-500 mt-1">

Complete loan information

</p>


</div>




<span class="inline-flex w-fit px-4 py-2 rounded-full text-sm font-semibold

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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">



<div class="bg-white rounded-2xl shadow-sm p-5">

<p class="text-xs text-gray-500">

Applicant

</p>


<h2 class="font-semibold text-lg mt-1">

{{ $loan->user->name }}

</h2>


<p class="text-sm text-gray-500">

{{ $loan->user->phone_no ?? '-' }}

</p>


</div>






<div class="bg-white rounded-2xl shadow-sm p-5">

<p class="text-xs text-gray-500">

Loan Amount

</p>


<h2 class="text-xl font-bold text-emerald-700 mt-1">

KES {{ number_format($loan->amount) }}

</h2>


</div>







<div class="bg-white rounded-2xl shadow-sm p-5">

<p class="text-xs text-gray-500">

Remaining Balance

</p>


<h2 class="text-xl font-bold text-blue-700 mt-1">

KES {{ number_format($loan->remaining_balance) }}

</h2>


</div>



</div>








{{-- REPAYMENT --}}

    {{-- REPAYMENT --}}

        @if(
    auth()->id() === $loan->user_id &&
    in_array($loan->status, ['approved', 'disbursed', 'overdue'])
)

<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6">

    <h2 class="font-semibold text-lg mb-4">
        Make Repayment
    </h2>

    <form method="POST" action="{{ route('mpesa.stk') }}">
        @csrf

        <input type="hidden" name="payment_type" value="loan_repayment">

        <input type="hidden" name="loan_id" value="{{ $loan->id }}">

        <label class="block text-sm text-slate-600 mb-2">
            Amount to repay
        </label>

        <input
            type="number"
            name="amount"
            placeholder="Enter amount"
            max="{{ $loan->remaining_balance }}"
            required
            class="w-full border rounded-xl px-4 py-3 mb-4 focus:ring-2 focus:ring-emerald-200 focus:outline-none">

        <button
            class="w-full sm:w-auto bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700">
            Pay Loan via M-Pesa
        </button>

    </form>

</div>

@endif









{{-- LOAN INFORMATION --}}

<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 mb-6">


<h2 class="font-semibold text-lg mb-5">

Loan Information

</h2>




<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">





<div>

<p class="text-xs text-gray-500">

Interest Rate

</p>


<p class="font-semibold">

{{ $loan->interest_rate }}%

</p>

</div>






<div>

<p class="text-xs text-gray-500">

Duration

</p>


<p class="font-semibold">

{{ $loan->duration_days }} days

</p>

</div>






<div>

<p class="text-xs text-gray-500">

Total Payable

</p>


<p class="font-semibold">

KES {{ number_format($loan->total_payable) }}

</p>


</div>






<div>

<p class="text-xs text-gray-500">

Due Date

</p>


<p class="font-semibold">

{{ $loan->due_date?->format('d M Y') ?? '-' }}

</p>


</div>



</div>






<div class="mt-6">


<p class="text-xs text-gray-500">

Reason

</p>


<p class="mt-2 text-slate-700 break-words">

{{ $loan->reason }}

</p>


</div>




</div>









{{-- APPROVAL HISTORY --}}


<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">


<h2 class="font-semibold text-lg mb-5">

Approval History

</h2>





@forelse($loan->approvals as $approval)



<div class="border-b last:border-none py-4">


<div class="flex flex-col sm:flex-row sm:justify-between gap-2">


<div>


<p class="font-medium">

{{ $approval->approver->name }}

</p>



<p class="text-sm

{{ $approval->decision === 'approved'
?'text-green-600'
:'text-red-600'
}}">

{{ ucfirst($approval->decision) }}

</p>


</div>




<p class="text-xs text-slate-400">

{{ $approval->approved_at?->format('d M Y H:i') }}

</p>



</div>





@if($approval->comment)


<p class="mt-3 text-sm text-slate-600 italic break-words">

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






</div>


</x-layouts.group>