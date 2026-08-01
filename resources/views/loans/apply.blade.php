<x-layouts.group
    :group="$group"
    :groups="$groups">


<div class="max-w-4xl mx-auto p-3 sm:p-5 lg:p-8">



<form action="{{ route('loans.store') }}" method="POST">

@csrf



<div class="bg-white rounded-2xl shadow-sm overflow-hidden">





{{-- HEADER --}}

<div class="p-5 sm:p-6 border-b border-green-50">


<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">



<div>

<h2 class="text-xl sm:text-2xl font-semibold text-[#063a2a]">

Apply for a Loan

</h2>


<p class="text-sm text-gray-500 mt-1">

Request a loan from your group and view repayment details instantly.

</p>


</div>





<div class="bg-emerald-50 rounded-xl p-4 md:text-right">


<p class="text-xs text-gray-500">

Available Group Funds

</p>


<p class="text-lg font-bold text-emerald-700">

KES {{ number_format($availableFunds) }}

</p>


</div>



</div>


</div>









{{-- FORM BODY --}}

<div class="p-5 sm:p-6 space-y-6">





{{-- AMOUNT + PERIOD --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">





<div>


<label class="block text-sm font-semibold text-slate-700 mb-2">

Loan Amount (KES)

</label>


<input

id="amount"

name="amount"

type="number"

min="1"

required


class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-200 focus:outline-none"


/>



@error('amount')

<p class="text-red-500 text-xs mt-2">

{{ $message }}

</p>

@enderror



</div>








<div>


<label class="block text-sm font-semibold text-slate-700 mb-2">

Repayment Period

</label>



<select

id="duration"

name="duration_days"

class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-200 focus:outline-none">


@for($i = 30; $i <= $group->settings->repayment_period_days; $i += 30)

<option value="{{ $i }}">

{{ $i / 30 }}

{{ $i == 30 ? 'Month' : 'Months' }}

</option>


@endfor


</select>



</div>





</div>









{{-- REASON --}}


<div>


<label class="block text-sm font-semibold text-slate-700 mb-2">

Reason for Loan

</label>



<textarea

name="reason"

rows="5"

required

class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-200 focus:outline-none"

></textarea>



</div>









{{-- CALCULATIONS --}}



<div class="grid grid-cols-1 md:grid-cols-2 gap-5">





<div class="rounded-xl border border-green-100 bg-emerald-50 p-5">


<p class="text-xs text-gray-500">

Loan Amount

</p>


<p class="text-xl font-semibold text-[#063a2a] mt-1">

KES <span id="loanAmount">0</span>

</p>




<div class="mt-5">


<p class="text-xs text-gray-500">

Interest (estimated)

</p>


<p class="font-medium text-[#063a2a]">

KES <span id="interestAmount">0</span>

</p>


</div>



</div>









<div class="rounded-xl border border-slate-100 p-5 flex flex-col justify-between">


<div>


<p class="text-xs text-gray-500">

Total Repayment

</p>


<p class="text-2xl font-bold text-blue-700 mt-1">

KES <span id="repayment">0</span>

</p>


</div>




<p class="text-xs text-gray-500 mt-5">

Interest rate:

<strong>

{{ $group->settings->interest_rate ?? 0 }}%

</strong>


</p>



</div>





</div>









<button

type="submit"

class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-semibold transition">


Submit Loan Request


</button>







</div>



</div>



</form>



</div>









<script>


const amount =
document.getElementById('amount');


const duration =
document.getElementById('duration');


const loanAmount =
document.getElementById('loanAmount');


const interestAmount =
document.getElementById('interestAmount');


const repayment =
document.getElementById('repayment');





function calculate(){


let amt =
parseFloat(amount.value || 0);



let months =
parseInt(duration.value || 30) / 30;



let rate =
{{ $group->settings->interest_rate ?? 0 }};



let interest =
amt * (rate / 100) * months;



let total =
amt + interest;





loanAmount.innerText =
amt ? amt.toLocaleString() : '0';



interestAmount.innerText =
interest ? interest.toLocaleString() : '0';



repayment.innerText =
total ? total.toLocaleString() : '0';



}




amount?.addEventListener(
'input',
calculate
);



duration?.addEventListener(
'change',
calculate
);



calculate();


</script>



</x-layouts.group>