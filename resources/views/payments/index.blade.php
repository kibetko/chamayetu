<x-layouts.group
    :group="$group"
    :groups="$groups">

<div class="min-h-screen bg-[#D9E3F4] p-6">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-8">

    <div>

        <h1 class="text-3xl font-bold text-slate-800">
            Payments
        </h1>

        <p class="text-slate-500 mt-1">
            Contribution history and member payments.
        </p>

    </div>

    <button
        x-data
        x-on:click="$dispatch('open-modal', 'mpesa-contribution')"
        type="button"
        class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700">

        Make Contribution

    </button>

</div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-2xl shadow-lg p-6">

                <p class="text-slate-500 text-sm">
                    Total Contributions
                </p>

                <h2 class="text-3xl font-bold text-emerald-600 mt-2">
                    KES {{ number_format($totalContributions, 2) }}
                </h2>

            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">

                <p class="text-slate-500 text-sm">
                    This Month
                </p>

                <h2 class="text-3xl font-bold text-blue-600 mt-2">
                    KES {{ number_format($thisMonth, 2) }}
                </h2>

            </div>

        </div>

        @forelse($payments as $date => $records)

            <div class="mb-8">

                <h2 class="text-lg font-bold text-slate-700 mb-4">
                    {{ $date }}
                </h2>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

                    <table class="w-full">

                        <thead class="bg-slate-100">

                            <tr>

                                <th class="text-left px-6 py-4">
                                    Member
                                </th>

                                <th class="text-left px-6 py-4">
                                    Amount
                                </th>

                                <th class="text-left px-6 py-4">
    Transaction Code
</th>

                                <th class="text-left px-6 py-4">
                                    Status
                                </th>

                                <th class="text-left px-6 py-4">
                                    Time
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($records as $payment)

                                <tr class="border-t">

                                    <td class="px-6 py-4">
                                        {{ $payment->user->name }}
                                    </td>

                                    <td class="px-6 py-4 font-semibold text-emerald-600">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td class="px-6 py-4 font-mono font-semibold text-blue-600">
    {{ $payment->receipt_number ?? '-' }}
</td>

                                    <td class="px-6 py-4">

                                        <span class="rounded-full bg-green-100 px-3 py-1 text-sm text-green-700">

                                            {{ ucfirst($payment->status) }}

                                        </span>

                                    </td>

                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $payment->paid_at->format('H:i') }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @empty

            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">

                <p class="text-slate-500">

                    No payment records found.

                </p>

            </div>

        @endforelse

    </div>
    <x-modal name="mpesa-contribution" maxWidth="lg">


<div class="bg-gradient-to-r from-emerald-600 to-green-700 p-6">

    <h2 class="text-2xl font-bold text-white">
        M-Pesa Contribution
    </h2>

    <p class="text-emerald-100 mt-2">
        Contribute securely using M-Pesa STK Push.
    </p>

</div>

<div class="p-6">

    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-5 mb-8">

        <h3 class="font-semibold text-emerald-800 mb-4">
            Payment Process
        </h3>

        <div class="space-y-5">

            <div class="flex gap-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow">
                    1
                </div>

                <div>
                    <p class="font-semibold text-slate-800">
                        Enter Contribution Amount
                    </p>

                    <p class="text-sm text-slate-500">
                        Specify how much you would like to contribute.
                    </p>
                </div>

            </div>

            <div class="ml-5 border-l-2 border-dashed border-slate-200 h-4"></div>

            <div class="flex gap-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow">
                    2
                </div>

                <div>
                    <p class="font-semibold text-slate-800">
                        Receive STK Push
                    </p>

                    <p class="text-sm text-slate-500">
                        A payment prompt will be sent to your registered phone number.
                    </p>
                </div>

            </div>

            <div class="ml-5 border-l-2 border-dashed border-slate-200 h-4"></div>

            <div class="flex gap-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow">
                    3
                </div>

                <div>
                    <p class="font-semibold text-slate-800">
                        Authorize Payment
                    </p>

                    <p class="text-sm text-slate-500">
                        Enter your M-Pesa PIN to complete the transaction.
                    </p>
                </div>

            </div>

            <div class="ml-5 border-l-2 border-dashed border-slate-200 h-4"></div>

            <div class="flex gap-4">

                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold shadow">
                    ✓
                </div>

                <div>
                    <p class="font-semibold text-slate-800">
                        Contribution Recorded
                    </p>

                    <p class="text-sm text-slate-500">
                        Your payment will automatically appear in the contribution history after confirmation.
                    </p>
                </div>

            </div>

        </div>

    </div>

    <form
        action="{{ route('mpesa.stk') }}"
        method="POST">

        @csrf

        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Contribution Amount (KES)
        </label>

        <input
            type="number"
            name="amount"
            min="1"
            required
            placeholder="Enter amount"
            class="w-full rounded-2xl border-slate-300 px-4 py-3 text-lg focus:border-emerald-500 focus:ring-emerald-500">

        <div class="mt-4 rounded-xl bg-slate-50 border p-4">

            <p class="text-sm text-slate-500">
                Payment will be requested on your registered M-Pesa phone number.
            </p>

        </div>

        <div class="flex justify-end gap-3 mt-8">

            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'mpesa-contribution')"
                class="px-5 py-3 rounded-xl border border-slate-300 hover:bg-slate-50">

                Cancel

            </button>

            <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 shadow-lg">

                Send STK Push

            </button>

        </div>

    </form>

</div>


</x-modal>


</div>

</x-layouts.group>