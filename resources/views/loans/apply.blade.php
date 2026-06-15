```blade
<x-layouts.group
    :group="$group"
    :groups="$groups">

<div class="max-w-4xl mx-auto p-6">

<form action="{{ route('loans.store') }}" method="POST">

    @csrf

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8">

            <h1 class="text-3xl font-bold text-white">
                Apply For Loan
            </h1>

            <p class="text-blue-100 mt-2">
                Submit a loan request to your group for approval.
            </p>

        </div>

        <div class="p-8 space-y-8">

            {{-- Amount --}}
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
                    placeholder="Enter loan amount"
                    class="w-full rounded-2xl border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500">

                @error('amount')
                    <p class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Duration --}}
            <div>

                <div class="flex justify-between items-center mb-3">

                    <label class="text-sm font-semibold text-slate-700">
                        Repayment Period
                    </label>

                    <span
                        id="durationBadge"
                        class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold text-sm">

                        1 Month

                    </span>

                </div>

                <input
                    id="duration"
                    name="duration_days"
                    type="range"
                    min="1"
                    max="{{ $group->settings->repayment_period_days }}"
                    value="1"
                    class="w-full">

                <div class="flex justify-between text-xs text-slate-400 mt-2">

                    <span>1 Day</span>

                    <span>
                        Max:
                        {{ $group->settings->repayment_period_days }}
                        Days
                    </span>

                </div>

                @error('duration_days')
                    <p class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Reason --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Reason For Loan
                </label>

                <textarea
                    name="reason"
                    rows="4"
                    required
                    placeholder="Explain why you need this loan..."
                    class="w-full rounded-2xl border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"></textarea>

                @error('reason')
                    <p class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Summary --}}
            <div
                class="rounded-2xl border border-blue-200 bg-blue-50 p-6">

                <h3 class="font-bold text-lg text-slate-800 mb-4">
                    Repayment Summary
                </h3>

                <div class="space-y-3">

                    <div class="flex justify-between">

                        <span class="text-slate-600">
                            Loan Amount
                        </span>

                        <strong>
                            KES
                            <span id="loanAmount">
                                0
                            </span>
                        </strong>

                    </div>

                    <div class="flex justify-between">

                        <span class="text-slate-600">
                            Interest Rate
                        </span>

                        <strong>
                            {{ $group->settings->interest_rate }}%
                            per month
                        </strong>

                    </div>

                    <div class="flex justify-between">

                        <span class="text-slate-600">
                            Interest Amount
                        </span>

                        <strong>
                            KES
                            <span id="interestAmount">
                                0
                            </span>
                        </strong>

                    </div>

                    <div class="border-t pt-4 flex justify-between">

                        <span class="font-bold text-slate-800">
                            Total Repayment
                        </span>

                        <strong
                            class="text-2xl text-blue-700">

                            KES
                            <span id="repayment">
                                0
                            </span>

                        </strong>

                    </div>

                </div>

            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold shadow-lg transition">

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

const repayment =
document.getElementById('repayment');

const interestAmount =
document.getElementById('interestAmount');

const loanAmount =
document.getElementById('loanAmount');

const durationBadge =
document.getElementById('durationBadge');

function calculate()
{
    let amt =
        parseFloat(amount.value || 0);

    let days =
    parseInt(duration.value);

    let months =
    days / 30;

    let rate =
        {{ $group->settings->interest_rate ?? 0 }};

    let interest =
        amt *
        (rate / 100) *
        months;

    let total =
        amt +
        interest;

    loanAmount.innerText =
        amt.toLocaleString();

    interestAmount.innerText =
        interest.toLocaleString();

    repayment.innerText =
        total.toLocaleString();

    durationBadge.innerText =
    days + (days === 1 ? ' Day' : ' Days');
}

amount.addEventListener(
    'input',
    calculate
);

duration.addEventListener(
    'input',
    calculate
);

calculate();

</script>

</x-layouts.group>
```
