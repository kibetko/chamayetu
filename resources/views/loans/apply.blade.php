<x-layouts.group :group="$group" :groups="$groups">

<div class="max-w-4xl mx-auto p-6">

    <form action="{{ route('loans.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <div class="p-6 border-b border-green-50 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-[#063a2a]">Apply for a Loan</h2>
                    <p class="text-sm text-gray-500 mt-1">Request a loan from your group and view repayment details instantly.</p>
                </div>

                <div class="text-right">
                    <div class="text-xs text-gray-500">Available Group Funds</div>
                    <div class="text-lg font-bold text-emerald-700">KES {{ number_format($availableFunds) }}</div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 gap-6">

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Loan Amount (KES)</label>
                        <input id="amount" name="amount" type="number" min="1" required
                               class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        @error('amount')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Repayment Period</label>
                        <select id="duration" name="duration_days" class="w-full rounded-lg border border-slate-200 px-4 py-3">
                            @for($i = 30; $i <= $group->settings->repayment_period_days; $i += 30)
                                <option value="{{ $i }}">{{ $i / 30 }} {{ $i == 30 ? 'Month' : 'Months' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Reason for Loan</label>
                    <textarea name="reason" rows="4" required class="w-full rounded-lg border border-slate-200 px-4 py-3"></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-4 items-stretch">
                    <div class="rounded-lg border border-green-50 bg-emerald-50 p-4">
                        <div class="text-xs text-gray-500">Loan Amount</div>
                        <div class="text-lg font-semibold text-[#063a2a]">KES <span id="loanAmount">0</span></div>

                        <div class="mt-3 text-xs text-gray-500">Interest (est)</div>
                        <div class="text-sm font-medium text-[#063a2a]">KES <span id="interestAmount">0</span></div>
                    </div>

                    <div class="rounded-lg border border-slate-100 p-4 flex flex-col justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Total Repayment</div>
                            <div class="text-2xl font-bold text-blue-700">KES <span id="repayment">0</span></div>
                        </div>

                        <div class="text-xs text-gray-500 mt-4">Interest rate: <strong>{{ $group->settings->interest_rate ?? 0 }}%</strong></div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold">Submit Loan Request</button>
            </div>

        </div>
    </form>

</div>

<script>
    const amount = document.getElementById('amount');
    const duration = document.getElementById('duration');
    const loanAmount = document.getElementById('loanAmount');
    const interestAmount = document.getElementById('interestAmount');
    const repayment = document.getElementById('repayment');

    function calculate() {
        let amt = parseFloat(amount.value || 0);
        let months = parseInt(duration.value) / 30;
        let rate = {{ $group->settings->interest_rate ?? 0 }};
        let interest = amt * (rate / 100) * months;
        let total = amt + interest;

        loanAmount.innerText = amt ? amt.toLocaleString() : '0';
        interestAmount.innerText = interest ? interest.toLocaleString() : '0';
        repayment.innerText = total ? total.toLocaleString() : '0';
    }

    amount?.addEventListener('input', calculate);
    duration?.addEventListener('change', calculate);
    calculate();
</script>

</x-layouts.group>