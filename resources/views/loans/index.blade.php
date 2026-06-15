<x-layouts.group
    :group="$group"
    :groups="$groups">


<div class="p-6">

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Loans
    </h1>

    <a
        href="{{ route('loans.apply') }}"
        class="bg-blue-600 text-white px-5 py-3 rounded-xl">

        Apply Loan

    </a>

</div>

<div class="grid md:grid-cols-3 gap-5 mb-8">

    <div class="bg-white p-6 rounded-2xl shadow">
        <p>Total Contributions</p>
        <h2 class="text-2xl font-bold">
            KES {{ number_format($totalContributions) }}
        </h2>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <p>Total Loaned</p>
        <h2 class="text-2xl font-bold">
            KES {{ number_format($totalLoaned) }}
        </h2>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <p>Available</p>
        <h2 class="text-2xl font-bold">
            KES {{ number_format($available) }}
        </h2>
    </div>

</div>

<div class="grid lg:grid-cols-2 gap-6">

<div class="bg-white p-6 rounded-2xl shadow">

    <h2 class="font-bold mb-4">
        My Loans
    </h2>

    @foreach($myLoans as $loan)

        <div class="border-b py-4">

            <p>
                KES {{ number_format($loan->amount) }}
            </p>

            <p>
                Status:
                {{ ucfirst($loan->status) }}
            </p>

            <p>
                Balance:
                KES {{ number_format($loan->remaining_balance) }}
            </p>

        </div>

    @endforeach

</div>

<div class="bg-white p-6 rounded-2xl shadow">

    <h2 class="font-bold mb-4">
        Group Loans
    </h2>

    @foreach($groupLoans as $loan)

        <div class="border-b py-4">

            <p>
                {{ $loan->user->name }}
            </p>

            <p>
                KES {{ number_format($loan->amount) }}
            </p>

            <p>
                Approvals:
                {{ $loan->approval_count }}/3
            </p>

            @if(
                $loan->status == 'pending' &&
                $loan->user_id != auth()->id()
            )

                <form
                    action="{{ route('loans.approve',$loan) }}"
                    method="POST">

                    @csrf

                    <button
                        class="mt-2 bg-green-600 text-white px-4 py-2 rounded">

                        Approve

                    </button>

                </form>

            @endif

            @if(
                $loan->status == 'approved' &&
                $group->isChairperson()
            )

                <form
                    action="{{ route('loans.disburse',$loan) }}"
                    method="POST">

                    @csrf

                    <button
                        class="mt-2 bg-blue-600 text-white px-4 py-2 rounded">

                        Disburse

                    </button>

                </form>

            @endif

        </div>

    @endforeach

</div>

</div>

</div>

</x-layouts.group>