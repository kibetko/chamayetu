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
                            <span class="px-2 py-1 rounded-full text-xs {{ $loan->status === 'approved'
                    ? 'bg-green-100 text-green-700'
                    : ($loan->status === 'rejected'
                        ? 'bg-red-100 text-red-700'
                        : ($loan->status === 'pending'
                            ? 'bg-yellow-100 text-yellow-700'
                            : 'bg-blue-100 text-blue-700'))
                }}</span>
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
                            <a href="{{ route('loans.show',$loan) }}"
class="text-emerald-600 hover:underline">

{{ $loan->user->name }}

</a>
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

                    {{-- APPROVAL TIMELINE --}}

@if($loan->approvals->count())

<div class="mt-5 border-t pt-4">

    <h3 class="text-xs font-semibold text-slate-500 uppercase mb-3">
        Approval History
    </h3>


    <div class="space-y-3">


    @foreach($loan->approvals as $approval)


    <div class="flex gap-3">


        {{-- ICON --}}

        <div class="mt-1">

            @if($approval->decision === 'approved')

                <div class="h-6 w-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs">
                    ✓
                </div>

            @else

                <div class="h-6 w-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs">
                    ✕
                </div>

            @endif

        </div>



        {{-- DETAILS --}}

        <div class="flex-1">


            <div class="flex justify-between">


                <p class="text-sm font-medium text-slate-700">

                    {{ $approval->approver->name ?? 'Unknown' }}

                </p>


                <span class="text-xs text-slate-400">

                    {{ $approval->approved_at 
                        ? \Carbon\Carbon::parse($approval->approved_at)->diffForHumans()
                        : '' 
                    }}

                </span>


            </div>



            <p class="text-xs
                {{ $approval->decision === 'approved'
                    ? 'text-green-600'
                    : 'text-red-600'
                }}">

                {{ ucfirst($approval->decision) }}

            </p>



            @if($approval->comment)

            <p class="mt-1 text-xs text-slate-500 italic">

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

                {{-- RIGHT ACTIONS --}}
                <div class="flex flex-col gap-2">

                   @if(
    $loan->status === 'pending'
    && $loan->user_id !== auth()->id()
    && $isOfficial
)

<div class="flex gap-2">

    {{-- APPROVE --}}
    <form action="{{ route('loans.approve',$loan) }}" method="POST">

@csrf


<textarea
name="comment"
placeholder="Approval comment (optional)"
class="w-full border rounded-lg p-2 text-sm mb-2"></textarea>


<button
class="bg-green-600 text-white px-3 py-1 rounded">
Approve
</button>


</form>


    {{-- REJECT --}}
    <button
        onclick="document.getElementById('reject-{{ $loan->id }}').classList.remove('hidden')"
        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">

        Reject

    </button>


</div>


{{-- REJECT MODAL --}}
<div
id="reject-{{ $loan->id }}"
class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">


<div class="bg-white rounded-xl p-6 w-full max-w-md">

<h3 class="font-semibold text-lg mb-4">
Reject Loan
</h3>


<form action="{{ route('loans.reject',$loan) }}" method="POST">

@csrf


<textarea
name="comment"
required
rows="4"
class="w-full border rounded-lg p-3"
placeholder="Reason for rejection">
</textarea>


<div class="flex justify-end gap-3 mt-4">


<button
type="button"
onclick="document.getElementById('reject-{{ $loan->id }}').classList.add('hidden')"
class="px-4 py-2 border rounded-lg">

Cancel

</button>


<button
class="bg-red-600 text-white px-4 py-2 rounded-lg">

Reject Loan

</button>


</div>


</form>


</div>

</div>

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