<x-layouts.group
    :group="$group"
    :groups="$groups">

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
                    <label for="paymentsSearch" class="sr-only">Search payments</label>
                    <input id="paymentsSearch" type="search" placeholder="Search by member or transaction..."
                        class="w-full md:w-80 pl-10 pr-4 py-3 rounded-2xl border border-slate-200 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500"
                    />
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </div>

                <button
                    x-data
                    x-on:click="$dispatch('open-modal', 'mpesa-contribution')"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-md hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                    Make Contribution
                </button>

            </div>

        </div>

        {{-- STATS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Total Contributions</p>
                        <h2 class="text-2xl font-bold text-emerald-600 mt-2">KES {{ number_format($totalContributions, 2) }}</h2>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-full">
                        <svg class="w-6 h-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v8M8 12h8" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-3">All-time contributions for this group.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Paid This Month</p>
                        <h2 class="text-2xl font-bold text-blue-600 mt-2">KES {{ number_format($paidThisMonth, 2) }}</h2>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v8M8 12h8" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-3">Payments recorded during the current month.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Remaining This Month</p>
                        <h2 class="text-2xl font-bold text-orange-600 mt-2">KES {{ number_format($remainingThisMonth, 2) }}</h2>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v8M8 12h8" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-3">Amount left to reach this month's target.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Due In</p>
                        <h2 class="text-2xl font-bold text-red-600 mt-2">{{ intval($daysRemaining) }} days</h2>
                        <p class="text-xs text-slate-400 mt-1">{{ $dueDate->format('d M Y') }}</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 8h18M7 8v10a1 1 0 001 1h8a1 1 0 001-1V8" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-3">Next due date for group contributions.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 col-span-1 sm:col-span-2 lg:col-span-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Penalty</p>
                        <h2 class="text-3xl font-extrabold text-red-600 mt-2">KES {{ number_format($penalty ?? 0, 2) }}</h2>
                    </div>
                    <div class="text-xs text-slate-400">Updated in real time</div>
                </div>
            </div>

        </div>

        {{-- PAYMENTS TABLE --}}
        <div id="paymentsContainer" class="space-y-8">

            @forelse($payments as $date => $records)

                <section class="payment-date-group bg-white rounded-2xl shadow-lg overflow-hidden" data-date="{{ $date }}">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <h2 class="text-lg font-semibold text-slate-700">{{ $date }}</h2>
                        <div class="text-sm text-slate-500">{{ $records->count() }} record{{ $records->count() > 1 ? 's' : '' }}</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px]">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs text-slate-500 uppercase tracking-wide">Member</th>
                                    <th class="text-left px-6 py-3 text-xs text-slate-500 uppercase tracking-wide">Amount</th>
                                    <th class="text-left px-6 py-3 text-xs text-slate-500 uppercase tracking-wide">Transaction</th>
                                    <th class="text-left px-6 py-3 text-xs text-slate-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left px-6 py-3 text-xs text-slate-500 uppercase tracking-wide">Time</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($records as $payment)
                                    <tr class="border-t hover:bg-slate-50 transition" data-name="{{ strtolower($payment->user->name) }}" data-code="{{ strtolower($payment->mpesaTransaction?->receipt_number ?? '') }}">
                                        <td class="px-6 py-4 flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-emerald-600 flex items-center justify-center text-white font-semibold">
                                                {{ \Str::limit(\Str::upper(\Illuminate\Support\Str::substr($payment->user->name,0,2)),2,'') }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-700">{{ $payment->user->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $payment->user->phone_no ?? '-' }}</div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 font-semibold text-emerald-600">KES {{ number_format($payment->amount, 2) }}</td>

                                        <td class="px-6 py-4 font-mono text-sm text-blue-600">{{ $payment->mpesaTransaction?->receipt_number ?? '-' }}</td>

                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                {{ $payment->status === 'paid'
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-slate-500">{{ $payment->paid_at->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </section>

            @empty

                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <p class="text-slate-500">
                        No payment records found.
                    </p>
                </div>

            @endforelse

        </div>

    </div>

    {{-- MODAL --}}
    <x-modal name="mpesa-contribution" maxWidth="lg">
        <div class="rounded-2xl overflow-hidden bg-white">

            {{-- HEADER --}}
            <div class="relative bg-gradient-to-r from-emerald-500 via-green-600 to-emerald-700 p-6">
                <div class="absolute inset-0 opacity-8 bg-[radial-gradient(circle_at_top_right,white,transparent)]"></div>

                <h2 class="text-2xl font-bold text-white flex items-center gap-2">💰 M-Pesa Contribution</h2>
                <p class="text-emerald-50/90 mt-2 text-sm">Secure STK Push payment directly to your phone.</p>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-6">
                <form action="{{ route('mpesa.stk') }}" method="POST">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Contribution Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">KES</span>
                            <input type="number" name="amount" min="1" required placeholder="0"
                                class="w-full pl-14 pr-4 py-4 text-lg rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition shadow-sm">
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Minimum contribution applies based on group settings.</p>
                    </div>

                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="text-emerald-600 text-xl">📱</div>
                            <div>
                                <p class="text-sm font-semibold text-emerald-800">M-Pesa STK Push</p>
                                <p class="text-sm text-emerald-700 mt-1">You will receive a prompt on your registered M-Pesa number to complete payment.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close-modal', 'mpesa-contribution')" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 active:scale-[0.98] transition shadow-md shadow-emerald-200">Send STK Push</button>
                    </div>
                </form>
            </div>

        </div>
    </x-modal>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('paymentsSearch');
    const container = document.getElementById('paymentsContainer');

    search.addEventListener('input', (e) => {
        const q = e.target.value.trim().toLowerCase();

        // iterate over date groups and rows
        document.querySelectorAll('.payment-date-group').forEach(group => {
            let anyVisible = false;
            group.querySelectorAll('tbody tr').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const code = row.getAttribute('data-code') || '';
                const visible = !q || name.includes(q) || code.includes(q);
                row.style.display = visible ? '' : 'none';
                if (visible) anyVisible = true;
            });

            group.style.display = anyVisible ? '' : 'none';
        });
    });
});
</script>
@endpush

</x-layouts.group>