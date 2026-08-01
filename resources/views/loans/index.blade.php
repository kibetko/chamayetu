<x-layouts.group 
    :group="$group" 
    :groups="$groups">

<div class="max-w-7xl mx-auto p-3 sm:p-5 lg:p-8">


{{-- HEADER --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#063a2a]">
            Loans
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Apply, approve and manage group loans.
        </p>
    </div>


    <a href="{{ route('loans.apply') }}"
       class="w-full sm:w-auto text-center bg-emerald-600 text-white px-5 py-3 rounded-xl shadow hover:bg-emerald-700 transition">

        + Apply for Loan

    </a>

</div>




{{-- SUMMARY CARDS --}}

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">


<div class="bg-white rounded-2xl p-5 shadow-sm flex justify-between items-center">

<div>

<p class="text-xs text-gray-500">
Total Contributions
</p>

<h2 class="text-xl font-bold text-emerald-700 mt-1">
KES {{ number_format($totalContributions) }}
</h2>

</div>

<div class="text-3xl">
💵
</div>

</div>



<div class="bg-white rounded-2xl p-5 shadow-sm flex justify-between items-center">

<div>

<p class="text-xs text-gray-500">
Total Loaned
</p>

<h2 class="text-xl font-bold text-blue-600 mt-1">
KES {{ number_format($totalLoaned) }}
</h2>

</div>

<div class="text-3xl">
🏦
</div>

</div>




<div class="bg-white rounded-2xl p-5 shadow-sm flex justify-between items-center sm:col-span-2 lg:col-span-1">

<div>

<p class="text-xs text-gray-500">
Available Funds
</p>

<h2 class="text-xl font-bold text-orange-600 mt-1">
KES {{ number_format($available) }}
</h2>

</div>


<div class="text-3xl">
🪙
</div>


</div>


</div>






<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">





{{-- MY LOANS --}}

<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">


<h2 class="font-semibold text-lg text-slate-800 mb-4">
My Loans
</h2>



@forelse($myLoans as $loan)


<div class="border-b last:border-none py-4">


<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">


<div>


<p class="font-semibold text-slate-800">

KES {{ number_format($loan->amount) }}

</p>


<p class="text-sm text-slate-500">

Balance:
KES {{ number_format($loan->remaining_balance) }}

</p>


</div>



<span
class="inline-flex w-fit px-3 py-1 rounded-full text-xs font-semibold

{{ $loan->status === 'approved'
    ? 'bg-green-100 text-green-700'
    : ($loan->status === 'rejected'
        ? 'bg-red-100 text-red-700'
        : ($loan->status === 'pending'
            ? 'bg-yellow-100 text-yellow-700'
            : 'bg-blue-100 text-blue-700'))
}}">


{{ ucfirst($loan->status) }}


</span>



</div>


</div>



@empty


<div class="py-8 text-center text-slate-500">

You have no loans.

</div>


@endforelse



</div>







{{-- GROUP LOANS --}}

<div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">


<h2 class="font-semibold text-lg text-slate-800 mb-5">

Group Loans

</h2>



@forelse($groupLoans as $loan)



@php

$progress = min(($loan->approval_count / 3) * 100,100);

@endphp




<div class="border rounded-xl p-4 mb-4 hover:bg-slate-50 transition">





<div class="flex flex-col sm:flex-row sm:justify-between gap-4">


<div class="flex-1">



<div class="flex flex-wrap items-center gap-2">


<a href="{{ route('loans.show',$loan) }}"
class="font-semibold text-emerald-600 hover:underline">

{{ $loan->user->name }}

</a>



<span class="px-3 py-1 rounded-full text-xs font-semibold

{{ $loan->status === 'approved'
? 'bg-green-100 text-green-700'
: ($loan->status === 'pending'
? 'bg-yellow-100 text-yellow-700'
: 'bg-blue-100 text-blue-700')
}}">


{{ ucfirst($loan->status) }}


</span>


</div>




<p class="mt-2 font-medium text-slate-700">

KES {{ number_format($loan->amount) }}

</p>





{{-- APPROVAL BAR --}}

<div class="mt-4 max-w-xs">


<div class="flex justify-between text-xs text-slate-500 mb-1">

<span>
Approvals
</span>


<span>
{{ $loan->approval_count }}/3
</span>


</div>


<div class="h-2 bg-slate-200 rounded-full overflow-hidden">

<div class="h-full bg-emerald-600 rounded-full"
style="width:{{ $progress }}%">
</div>


</div>


</div>






{{-- APPROVAL HISTORY --}}


@if($loan->approvals->count())


<div class="mt-5 border-t pt-4">


<p class="text-xs uppercase text-slate-500 font-semibold mb-3">

Approval History

</p>



<div class="space-y-3">


@foreach($loan->approvals as $approval)



<div class="flex gap-3">


<div>

@if($approval->decision==='approved')

<div class="h-7 w-7 rounded-full bg-green-100 text-green-600 flex items-center justify-center">

✓

</div>

@else

<div class="h-7 w-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center">

✕

</div>

@endif


</div>



<div class="flex-1">


<div class="flex flex-col sm:flex-row sm:justify-between">


<p class="font-medium text-sm">

{{ $approval->approver->name ?? 'Unknown' }}

</p>


<p class="text-xs text-slate-400">

{{ $approval->approved_at 
? \Carbon\Carbon::parse($approval->approved_at)->diffForHumans()
: ''
}}

</p>


</div>



<p class="text-xs mt-1

{{ $approval->decision==='approved'
?'text-green-600'
:'text-red-600'
}}">

{{ ucfirst($approval->decision) }}

</p>



@if($approval->comment)

<p class="text-xs text-slate-500 italic mt-1">

"{{ $approval->comment }}"

</p>

@endif


</div>



</div>


@endforeach


</div>


</div>


@endif




</div>





{{-- ACTIONS --}}

<div class="sm:w-40">


@if(
$loan->status==='pending'
&&
$loan->user_id !== auth()->id()
&&
$isOfficial
)



<form action="{{ route('loans.approve',$loan) }}" method="POST">

@csrf


<textarea
name="comment"
placeholder="Approval comment"
class="w-full border rounded-lg p-2 text-sm mb-2">
</textarea>



<button
class="w-full bg-green-600 text-white py-2 rounded-lg">

Approve

</button>


</form>



@endif




@if($loan->status=='approved' && $group->isChairperson())


<form action="{{ route('loans.disburse',$loan) }}" method="POST">

@csrf


<button
class="w-full bg-blue-600 text-white py-2 rounded-lg">

Disburse

</button>


</form>


@endif



</div>



</div>




</div>



@empty


<div class="text-center text-slate-500 py-8">

No group loans found.

</div>


@endforelse



</div>



</div>



</div>

</x-layouts.group>