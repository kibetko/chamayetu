<x-layouts.group
    :group="$group"
    :groups="$groups">

<div class="flex h-screen bg-gray-100">

    <!-- Mobile Overlay -->
    <div
        id="overlay"
        class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
    </div>

    <!-- Sidebar -->
    

    <!-- Main -->
    <main class="flex-1 overflow-y-auto">

        <!-- Header -->
        <div class="bg-white shadow p-4 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <button
                    id="menuButton"
                    class="lg:hidden">

                    ☰

                </button>

                <div>

                    <h2 class="text-2xl font-bold">
                        {{ $group->name }}
                    </h2>

                    <p class="text-gray-500">
                        {{ $group->description }}
                    </p>

                </div>

            </div>

            <div class="flex items-center">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-md">
                <div>{{ Auth::user()->name }}</div>

                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
                Profile
            </x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link
                    :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    Log Out
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>

        </div>

        <!-- Content -->
        <div class="p-6">

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-gray-500">Members</h3>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ $stats['members'] }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-gray-500">Total Contributions</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        KES {{ number_format($stats['contributions']) }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-gray-500">Active Loans</h3>
                    <p class="text-3xl font-bold text-orange-600 mt-2">
                        {{ $stats['active_loans'] }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-gray-500">Join Requests</h3>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ $stats['pending_requests'] }}
                    </p>
                </div>

            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-6">

    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-gray-500">Total Loaned</h3>

        <p class="text-3xl font-bold text-blue-600 mt-2">
            KES {{ number_format($loanStats['total_loaned']) }}
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-gray-500">Total Repaid</h3>

        <p class="text-3xl font-bold text-green-600 mt-2">
            KES {{ number_format($loanStats['total_repaid']) }}
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-gray-500">Outstanding Balance</h3>

        <p class="text-3xl font-bold text-orange-600 mt-2">
            KES {{ number_format($loanStats['outstanding']) }}
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-gray-500">Recovery Rate</h3>

        <p class="text-3xl font-bold text-purple-600 mt-2">
            {{ number_format($recoveryRate, 1) }}%
        </p>
    </div>

</div>
<div class="bg-white rounded-xl shadow mt-8">

    <div class="p-4 border-b">
        <h3 class="font-bold">
            Top Borrowers
        </h3>
    </div>

    <div class="p-4">

        @forelse($topBorrowers as $borrower)

            <div class="flex justify-between py-3 border-b">

                <span>
                    {{ $borrower->user->name }}
                </span>

                <span class="font-semibold text-blue-600">
                    KES {{ number_format($borrower->total_borrowed) }}
                </span>

            </div>

        @empty

            <p class="text-gray-500">
                No loans recorded yet.
            </p>

        @endforelse

    </div>

</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="text-gray-500">
            Active Loans
        </h3>

        <p class="text-3xl font-bold text-blue-600 mt-2">
            {{ $loanStats['active_loans'] }}
        </p>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="text-gray-500">
            Completed Loans
        </h3>

        <p class="text-3xl font-bold text-green-600 mt-2">
            {{ $loanStats['completed_loans'] }}
        </p>

    </div>

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="text-gray-500">
            Overdue Loans
        </h3>

        <p class="text-3xl font-bold text-red-600 mt-2">
            {{ $loanStats['overdue_loans'] }}
        </p>

    </div>

</div>

<div class="bg-white rounded-xl shadow mt-8">

    <div class="p-4 border-b">
        <h3 class="font-bold">
            Monthly Loan Disbursements
        </h3>
    </div>

    <div class="p-4">
        <canvas id="loanChart"></canvas>
    </div>

</div>
            

            <!-- Activity + Requests -->
            <div class="grid lg:grid-cols-2 gap-6 mt-8">

                <!-- Activity -->
                <div class="bg-white rounded-xl shadow">

                    <div class="p-4 border-b">
                        <h3 class="font-bold">
                            Recent Activity
                        </h3>
                    </div>

                    <div class="p-4">
                        No activity yet.
                    </div>

                </div>

                <!-- Pending Requests -->
                <div class="bg-white rounded-xl shadow">

                    <div class="p-4 border-b">
                        <h3 class="font-bold">
                            Pending Requests
                        </h3>
                    </div>

                    <div class="p-4">
                        @php

                        $currentMember = $group->members
                            ->where(
                                'user_id',
                                auth()->id()
                            )
                            ->first();

                        @endphp
                        @php
                        $isChairperson =
                            $currentMember &&
                            $currentMember->role === 'chairperson';
                        @endphp

                        @forelse(
                            $group->joinRequests()
                                ->where('status','pending')
                                ->latest()
                                ->get()
                            as $request
                        )

                            <div class="border-b py-3">

                                <div class="flex justify-between items-center">

                                    <div>

                                        <p class="font-semibold">
                                            {{ $request->user->name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $request->phone_number }}
                                        </p>

                                        @if($request->message)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $request->message }}
                                            </p>
                                        @endif

                                    </div>

                                    <div class="flex gap-2">

                                        <form
                                            method="POST"
                                            action="{{ route('join-requests.approve', $request->id) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="bg-green-600 text-white px-3 py-1 rounded">

                                                Approve

                                            </button>

                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('join-requests.reject', $request->id) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded">

                                                Reject

                                            </button>

                                        </form>

                                        </div>

                                        </div>

                                            @empty

                                                <p class="text-gray-500">
                                                    No pending requests
                                                </p>

                                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

    </main>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('loanChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyLoans->pluck('month')),
        datasets: [{
            label: 'Loan Amount',
            data: @json($monthlyLoans->pluck('total')),
            borderWidth: 3,
            tension: 0.3
        }]
    },
    options: {
        responsive: true
    }
});
</script>



</x-layouts.group>