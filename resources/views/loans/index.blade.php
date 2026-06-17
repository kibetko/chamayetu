<x-layouts.group :group="$group" :groups="$groups">

<div class="p-6 max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#063a2a]">Loans</h1>
            <p class="text-sm text-gray-500 mt-1">Apply, approve and manage group loans.</p>
        </div>

        <a href="{{ route('loans.apply') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow hover:bg-emerald-700">+ Apply for Loan</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Total Contributions</div>
                <div class="text-xl font-semibold text-emerald-700">KES {{ number_format($totalContributions) }}</div>
            </div>
            <div class="text-2xl text-emerald-200">💵</div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Total Loaned</div>
                <div class="text-xl font-semibold text-blue-600">KES {{ number_format($totalLoaned) }}</div>
            </div>
            <div class="text-2xl text-blue-100">🏦</div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Available</div>
                <div class="text-xl font-semibold text-orange-600">KES {{ number_format($available) }}</div>
            </div>
            <div class="text-2xl text-orange-100">🪙</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-6 shadow">
            <h2 class="font-semibold mb-4">My Loans</h2>

            @forelse($myLoans as $loan)
                <div class="border-b py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">KES {{ number_format($loan->amount) }}</div>
                            <div class="text-xs text-slate-500">Balance: KES {{ number_format($loan->remaining_balance) }}</div>
                        </div>
                        <div class="text-sm">
                            <span class="px-2 py-1 rounded-full text-xs {{ $loan->status === 'paid' ? 'bg-green-100 text-green-700' : ($loan->status === 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($loan->status) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-6 text-slate-500">You have no loans.</div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl p-6 shadow">
    <h2 class="font-semibold mb-4">Group Loans</h2>

    @forelse($groupLoans as $loan)

        @php
            $progress = ($loan->approval_count / 3) * 100;
        @endphp

        <div class="border-b py-5 hover:bg-slate-50 transition rounded-lg px-3">

            <div class="flex items-start justify-between">

                {{-- LEFT INFO --}}
                <div class="space-y-2">

                    <div class="flex items-center gap-2">
                        <div class="text-sm font-semibold text-slate-800">
                            {{ $loan->user->name }}
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $loan->status === 'approved'
                                ? 'bg-green-100 text-green-700'
                                : ($loan->status === 'pending'
                                    ? 'bg-yellow-100 text-yellow-700'
                                    : 'bg-blue-100 text-blue-700') }}">

                            {{ ucfirst($loan->status) }}

                        </span>
                    </div>

                    <div class="text-sm text-slate-600 font-medium">
                        KES {{ number_format($loan->amount) }}
                    </div>

                    {{-- ⭐ ROUND PROGRESS BAR --}}
                    <div class="w-48">
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Approvals</span>
                            <span>{{ $loan->approval_count }}/3</span>
                        </div>

                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div
                                class="h-2 bg-gradient-to-r from-emerald-400 to-green-600 rounded-full transition-all duration-500"
                                style="width: {{ $progress }}%">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT ACTIONS --}}
                <div class="flex flex-col gap-2">

                    @if(
    $loan->status === 'pending'
    && $loan->user_id !== auth()->id()
    && $isOfficial
)
    <form action="{{ route('loans.approve', $loan) }}" method="POST">
        @csrf

        <button
            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">
            Approve
        </button>
    </form>
@endif

                    @if($loan->status == 'approved' && $group->isChairperson())
                        <form action="{{ route('loans.disburse',$loan) }}" method="POST">
                            @csrf
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm shadow">
                                Disburse
                            </button>
                        </form>
                    @endif

                </div>

            </div>
        </div>

    @empty
        <div class="py-6 text-slate-500">No group loans found.</div>
    @endforelse
</div>
    </div>

</div>

</x-layouts.group>