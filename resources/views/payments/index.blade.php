<x-layouts.group :group="$group" :groups="$groups">

<div class="min-h-screen bg-gradient-to-b from-[#EAF6F0] to-[#D9EAF2] p-6">

    <div class="max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">

            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                    Payments
                </h1>
                <p class="text-slate-500 mt-1">
                    Contribution history and member payments.
                </p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">

                <div class="relative flex-1 md:flex-none">
                    <input id="paymentsSearch" type="search"
                        placeholder="Search member or transaction..."
                        class="w-full md:w-80 pl-10 pr-4 py-3 rounded-2xl border border-slate-200 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                </div>

                <button
                    x-data
                    x-on:click="$dispatch('open-modal', 'mpesa-contribution')"
                    class="flex items-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow hover:bg-emerald-700 transition">
                    + Contribute
                </button>

            </div>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            @foreach([
                ['label'=>'Total Contributions','value'=>$totalContributions,'color'=>'emerald'],
                ['label'=>'Paid This Month','value'=>$paidThisMonth,'color'=>'blue'],
                ['label'=>'Remaining','value'=>$remainingThisMonth,'color'=>'orange'],
                ['label'=>'Due In','value'=>((int) $daysRemaining).' days','color'=>'red']
            ] as $stat)
            

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition">
                <p class="text-slate-500 text-sm">{{ $stat['label'] }}</p>
                <h2 class="text-2xl font-bold text-{{ $stat['color'] }}-600 mt-2">
                    {{ is_numeric($stat['value']) ? 'KES '.number_format($stat['value'],2) : $stat['value'] }}
                </h2>
            </div>

            @endforeach

        </div>

        {{--SUMMARY --}}
        {{-- MEMBER CONTRIBUTION SUMMARY --}}

<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">

    <div class="px-6 py-4 border-b flex items-center justify-between">

        <h2 class="font-semibold text-slate-700">
            Member Contribution Summary
        </h2>

        <span class="text-sm text-slate-500">
            Monthly Requirement:
            KES {{ number_format($group->settings?->minimum_contribution ?? 0, 2) }}
        </span>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full min-w-[900px]">

            <thead class="bg-slate-50 text-xs uppercase text-slate-500">

                <tr>
                    <th class="px-6 py-3 text-left">Member</th>
                    <th class="px-6 py-3 text-left">Total Contributions</th>
                    <th class="px-6 py-3 text-left">This Month</th>
                    <th class="px-6 py-3 text-left">Remaining</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>

            </thead>

            <tbody>

                @forelse($memberPayments as $member)

                <tr class="border-t hover:bg-slate-50">

                    <td class="px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div class="h-10 w-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">

                                {{ strtoupper(substr($member['user']->name, 0, 2)) }}

                            </div>

                            <div>

                                <div class="font-medium">
                                    {{ $member['user']->name }}
                                </div>

                                <div class="text-xs text-slate-400">
                                    {{ $member['user']->phone_no ?? '-' }}
                                </div>

                            </div>

                        </div>

                    </td>

                    <td class="px-6 py-4 font-semibold text-blue-600">

                        KES {{ number_format($member['total_paid'], 2) }}

                    </td>

                    <td class="px-6 py-4 font-semibold text-emerald-600">

                        KES {{ number_format($member['paid_this_month'], 2) }}

                    </td>

                    <td class="px-6 py-4">

                        @if($member['remaining'] > 0)

                            <span class="font-semibold text-orange-600">

                                KES {{ number_format($member['remaining'], 2) }}

                            </span>

                        @else

                            <span class="font-semibold text-green-600">

                                Fully Paid

                            </span>

                        @endif

                    </td>

                    <td class="px-6 py-4">

                        @if($member['status'] === 'Complete')

                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">

                                Complete

                            </span>

                        @else

                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">

                                Pending

                            </span>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">

                        No contributions found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

        {{-- PAYMENTS --}}
        <div id="paymentsContainer" class="space-y-8">

            @forelse($payments as $date => $records)

            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">

                <div class="px-6 py-4 border-b flex justify-between">
                    <h2 class="font-semibold text-slate-700">{{ $date }}</h2>
                    <span class="text-sm text-slate-400">{{ $records->count() }} records</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px]">

                        <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Member</th>
                                <th class="px-6 py-3 text-left">Amount</th>
                                <th class="px-6 py-3 text-left">Transaction</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Time</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($records as $payment)
                        <tr class="border-t hover:bg-slate-50 transition"
                            data-name="{{ strtolower($payment->user->name) }}"
                            data-code="{{ strtolower($payment->mpesaTransaction?->receipt_number ?? '') }}">

                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($payment->user->name,0,2)) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $payment->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $payment->user->phone_no ?? '-' }}</div>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                KES {{ number_format($payment->amount,2) }}
                            </td>

                            <td class="px-6 py-4 font-mono text-sm text-blue-600">
                                {{ $payment->mpesaTransaction?->receipt_number ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $payment->status === 'paid'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $payment->paid_at?->format('H:i') }}
                            </td>

                        </tr>
                        @endforeach

                        </tbody>

                    </table>
                </div>

            </section>

            @empty
                <div class="bg-white rounded-2xl shadow p-10 text-center text-slate-500">
                    No payment records found.
                </div>
            @endforelse

        </div>

    </div>

    {{-- MODAL --}}
    <x-modal name="mpesa-contribution" maxWidth="lg">

        <div class="bg-white rounded-2xl overflow-hidden">

            <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-white">
                <h2 class="text-xl font-bold">M-Pesa Contribution</h2>
                <p class="text-sm opacity-90 mt-1">Send STK push to complete payment</p>
            </div>

            <div class="p-6">

                <form action="{{ route('mpesa.stk') }}" method="POST">
                    @csrf

                    <input type="number" name="amount" min="1" required
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-200"
                        placeholder="Enter amount">

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button"
                            x-on:click="$dispatch('close-modal', 'mpesa-contribution')"
                            class="px-4 py-2 rounded-xl border">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-600 text-white">
                            Pay
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </x-modal>

</div>

{{-- SEARCH SCRIPT --}}
<script>
document.getElementById('paymentsSearch').addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase();

    document.querySelectorAll('tbody tr').forEach(row => {
        const name = row.dataset.name || '';
        const code = row.dataset.code || '';

        row.style.display =
            name.includes(q) || code.includes(q) || q === ''
            ? ''
            : 'none';
    });
});
</script>

</x-layouts.group>