
<x-layouts.group
    :group="$group"
    :groups="$groups"
>

    <!-- Mobile Overlay -->
    <div
        id="overlay"
        class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
    </div>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto p-6">

        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        
<div class="mb-6 flex items-center justify-between">

    <!-- Dashboard heading -->
    <div>

        <h1 class="text-2xl font-bold text-[#0f3a2b]">
            Dashboard Overview
        </h1>

        <p class="text-sm text-gray-600 mt-1">
            Welcome back, {{ Auth::user()->name }}
        </p>

    </div>


    <!-- ========================================================= -->
    <!-- HEADER ACTIONS -->
    <!-- ========================================================= -->

    <div class="flex items-center gap-3">


        <!-- ===================================================== -->
        <!-- NOTIFICATIONS -->
        <!-- ===================================================== -->

        <div class="relative">

            <button
                onclick="document.getElementById('notification-dropdown').classList.toggle('hidden')"
                class="relative flex items-center justify-center
                       w-10 h-10 rounded-full
                       hover:bg-emerald-50 transition"
                type="button"
                title="Notifications"
            >

                <span class="text-xl">
                    🔔
                </span>


                @if(auth()->user()->unreadNotifications->count())

                    <span
                        class="absolute -top-1 -right-1
                               bg-red-600 text-white
                               text-[10px] font-bold
                               rounded-full
                               min-w-[20px] h-5
                               px-1
                               flex items-center justify-center
                               border-2 border-white"
                    >
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>

                @endif

            </button>


            <!-- Notification dropdown -->

            <div
                id="notification-dropdown"
                class="hidden absolute right-0 mt-3
                       w-80 sm:w-96
                       bg-white rounded-2xl
                       shadow-xl border border-gray-100
                       z-50 overflow-hidden"
            >

                <!-- Header -->

                <div class="p-4 border-b bg-gray-50">

                    <div class="flex items-center justify-between">

                        <h3 class="font-semibold text-[#063a2a]">
                            Notifications
                        </h3>

                        @if(auth()->user()->unreadNotifications->count())

                            <span
                                class="text-xs
                                       bg-red-100 text-red-600
                                       px-2 py-1 rounded-full"
                            >
                                {{ auth()->user()->unreadNotifications->count() }}
                                new
                            </span>

                        @endif

                    </div>

                </div>


                <!-- Notifications -->

                <div class="max-h-80 overflow-y-auto">

                    @forelse(
                        auth()->user()
                            ->notifications()
                            ->latest()
                            ->take(10)
                            ->get()
                        as $notification
                    )

                        <div
                            class="p-4 border-b
                                   hover:bg-gray-50
                                   transition"
                        >

                            <div class="flex gap-3">

                                <div
                                    class="w-9 h-9 rounded-full
                                           bg-emerald-100
                                           flex items-center justify-center
                                           flex-shrink-0"
                                >
                                    🔔
                                </div>

                                <div class="min-w-0">

                                    <div
                                        class="font-semibold text-sm
                                               text-gray-800"
                                    >
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </div>

                                    <div
                                        class="text-xs text-gray-500 mt-1"
                                    >
                                        {{ $notification->data['message'] ?? '' }}
                                    </div>

                                    <div
                                        class="text-[11px] text-gray-400 mt-2"
                                    >
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="p-8 text-center">

                            <div class="text-3xl mb-2">
                                🔕
                            </div>

                            <p class="text-sm text-gray-500">
                                No notifications
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>



        <!-- ===================================================== -->
        <!-- PROFILE -->
        <!-- ===================================================== -->

        <div class="relative">

            <button
                type="button"
                onclick="document.getElementById('profile-dropdown').classList.toggle('hidden')"
                class="flex items-center gap-2
                       p-1.5 pr-3
                       rounded-full
                       hover:bg-emerald-50
                       transition"
            >

                <!-- Avatar -->

                <div
                    class="w-10 h-10
                           rounded-full
                           bg-gradient-to-br
                           from-emerald-600
                           to-emerald-800
                           text-white
                           flex items-center justify-center
                           font-semibold
                           shadow-sm"
                >
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>


                <!-- Name -->

                <div class="hidden md:block text-left">

                    <div class="text-sm font-semibold text-gray-800">
                        {{ Auth::user()->name }}
                    </div>

                    <div class="text-[11px] text-gray-500">
                        My Account
                    </div>

                </div>


                <!-- Arrow -->

                <svg
                    class="hidden md:block w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>

            </button>


            <!-- Profile dropdown -->

            <div
                id="profile-dropdown"
                class="hidden absolute right-0 mt-3
                       w-64
                       bg-white
                       rounded-2xl
                       shadow-xl
                       border border-gray-100
                       z-50
                       overflow-hidden"
            >

                <!-- Profile summary -->

                <div class="p-4 bg-gray-50 border-b">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11
                                   rounded-full
                                   bg-gradient-to-br
                                   from-emerald-600
                                   to-emerald-800
                                   text-white
                                   flex items-center justify-center
                                   font-bold"
                        >
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0">

                            <div class="font-semibold text-gray-800 truncate">
                                {{ Auth::user()->name }}
                            </div>

                            <div class="text-xs text-gray-500 truncate">
                                {{ Auth::user()->email }}
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Menu -->

                <div class="p-2">


                    <!-- Profile -->

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3
                               px-3 py-2.5
                               rounded-xl
                               text-sm text-gray-700
                               hover:bg-emerald-50
                               hover:text-emerald-700
                               transition"
                    >

                        <span
                            class="w-8 h-8 rounded-lg
                                   bg-emerald-100
                                   flex items-center justify-center"
                        >
                            👤
                        </span>

                        <div>

                            <div class="font-medium">
                                My Profile
                            </div>

                            <div class="text-[11px] text-gray-400">
                                View your profile
                            </div>

                        </div>

                    </a>


                    <!-- Edit Profile -->

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3
                               px-3 py-2.5
                               rounded-xl
                               text-sm text-gray-700
                               hover:bg-emerald-50
                               hover:text-emerald-700
                               transition"
                    >

                        <span
                            class="w-8 h-8 rounded-lg
                                   bg-blue-100
                                   flex items-center justify-center"
                        >
                            ✏️
                        </span>

                        <div>

                            <div class="font-medium">
                                Edit Profile
                            </div>

                            <div class="text-[11px] text-gray-400">
                                Update your details
                            </div>

                        </div>

                    </a>


                </div>


                <!-- Logout -->

                <div class="p-2 border-t">

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="w-full flex items-center gap-3
                                   px-3 py-2.5
                                   rounded-xl
                                   text-sm text-red-600
                                   hover:bg-red-50
                                   transition
                                   text-left"
                        >

                            <span
                                class="w-8 h-8 rounded-lg
                                       bg-red-100
                                       flex items-center justify-center"
                            >
                                🚪
                            </span>

                            <div>

                                <div class="font-medium">
                                    Logout
                                </div>

                                <div class="text-[11px] text-red-400">
                                    Sign out of your account
                                </div>

                            </div>

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>




        <!-- ========================================================= -->
        <!-- STAT CARDS -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- Group Contributions -->

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">
    <div class="text-xs text-gray-500">
        Total Available
    </div>

    <div class="mt-3 text-2xl font-semibold text-[#063a2a]">
        KES {{ number_format($stats['total_available']) }}
    </div>

    <div class="mt-2 text-xs text-gray-500">
        Contributions:
        <span class="font-semibold text-gray-700">
            KES {{ number_format($stats['contributions']) }}
        </span>
    </div>
</div>


            <!-- My Contributions -->

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">

                <div class="text-xs text-gray-500">
                    My Contribution Summary
                </div>

                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">

                    KES {{ number_format($stats['my_contributions']) }}

                </div>

            </div>


            <!-- Total Loans -->

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">

                <div class="text-xs text-gray-500">
                    Total Loans Issued
                </div>

                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">

                    KES {{ number_format($loanStats['total_loaned']) }}

                </div>

                <div class="text-xs text-gray-400 mt-1">
                    Principal only
                </div>

            </div>


            <!-- Outstanding -->

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">

                <div class="text-xs text-gray-500">
                    Outstanding Loan Balances
                </div>

                <div
                    class="mt-3 text-2xl font-semibold
                    {{ $loanStats['outstanding'] > 0
                        ? 'text-orange-600'
                        : 'text-green-600'
                    }}"
                >

                    KES {{ number_format($loanStats['outstanding']) }}

                </div>

                <div class="text-xs text-gray-400 mt-1">
                    Including interest
                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PREPARE CHART DATA -->
        <!-- ========================================================= -->

        @php

            $dailyContribLabels = $dailyContributions->map(function ($item) {
    return \Carbon\Carbon::parse($item->contribution_date)
        ->format('d M');
});

$dailyContribData = $dailyContributions->pluck('total');


            /*
            |--------------------------------------------------------------------------
            | CONTRIBUTORS
            |--------------------------------------------------------------------------
            */

            if (isset($topContributors)) {

                $contributors = $topContributors->map(function ($c) {

                    return (object) [

                        'name' =>
                            $c->user->name ?? 'Unknown',

                        'total' =>
                            $c->total_contributed ?? 0,

                    ];

                });

            } elseif (isset($topBorrowers)) {

                $contributors = $topBorrowers->map(function ($b) {

                    return (object) [

                        'name' =>
                            $b->user->name ?? 'Unknown',

                        'total' =>
                            $b->total_borrowed ?? 0,

                    ];

                });

            } else {

                $contributors = collect();

            }


            $contributorsLabels =
                $contributors->pluck('name');

            $contributorsData =
                $contributors->pluck('total');


            /*
            |--------------------------------------------------------------------------
            | MONTHLY LOAN DATA
            |--------------------------------------------------------------------------
            */

            $loanChartLabels =
                $monthlyLoans->pluck('month');

            $loanChartData =
                $monthlyLoans->pluck('total');

        @endphp


        <!-- ========================================================= -->
        <!-- CONTRIBUTION CHARTS -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

            <!-- Monthly Contributions -->

             <div class="bg-white rounded-2xl shadow-sm p-6 border border-green-50">

                <div class="flex items-start justify-between">

                    <div>
                        <h3 class="text-lg font-semibold text-[#063a2a]">
                            Monthly Contributions
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Contribution performance over time
                        </p>
                    </div>

                <div class="flex items-center gap-2
                            bg-emerald-50 text-emerald-700
                            px-3 py-1.5 rounded-full text-xs font-semibold">

                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                    Contributions

                </div>

            </div>

            <div class="mt-5 h-64">
                <canvas id="monthlyContribChart"></canvas>
            </div>

            </div>


            <!-- Top Contributors -->

            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="text-lg font-semibold text-[#063a2a]">
                            Top Contributors
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Contributions by member
                        </p>

                    </div>

                    <div class="text-sm text-gray-500">
                        KES
                    </div>

                </div>


                <div class="mt-4">

                    <canvas
                        id="contributorsChart"
                        style="max-height:220px;"
                    ></canvas>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- LOAN HEALTH + RECENT ACTIVITY -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">


            <!-- ===================================================== -->
            <!-- LOAN HEALTH -->
            <!-- ===================================================== -->

            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                <div class="flex items-start justify-between">

                    <div>

                        <h3 class="text-lg font-semibold text-[#063a2a]">
                            Loan Health
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Principal, interest and repayment overview
                        </p>

                    </div>

                    <div class="text-sm text-gray-500">

                        KES {{ number_format($loanStats['outstanding']) }}

                        outstanding

                    </div>

                </div>


                <div class="mt-6 flex items-center gap-6">


                    <!-- DONUT -->

                    <div class="w-36 h-36 flex-shrink-0">

                        <canvas id="donutHealth"></canvas>

                    </div>


                    <!-- DETAILS -->

                    <div class="flex-1">


                        <!-- Principal -->

                        <div class="text-sm text-gray-600">
                            Principal Loaned
                        </div>

                        <div class="font-semibold text-blue-600">

                            KES
                            {{ number_format($loanStats['total_loaned']) }}

                        </div>


                        <!-- Interest -->

                        <div class="mt-4 text-sm text-gray-600">
                            Interest
                        </div>

                        <div class="font-semibold text-purple-600">

                            KES
                            {{ number_format($loanStats['total_interest']) }}

                        </div>


                        <!-- Total Payable -->

                        <div class="mt-4 text-sm text-gray-600">
                            Total Payable
                        </div>

                        <div class="font-semibold text-[#063a2a]">

                            KES
                            {{ number_format($loanStats['total_payable']) }}

                        </div>


                        <!-- Repaid -->

                        <div class="mt-4 text-sm text-gray-600">
                            Repaid
                        </div>

                        <div class="font-semibold text-green-600">

                            KES
                            {{ number_format($loanStats['total_repaid']) }}

                        </div>


                        <!-- Outstanding -->

                        <div class="mt-4 text-sm text-gray-600">
                            Outstanding
                        </div>

                        <div
                            class="font-semibold
                            {{ $loanStats['outstanding'] > 0
                                ? 'text-orange-600'
                                : 'text-green-600'
                            }}"
                        >

                            KES
                            {{ number_format($loanStats['outstanding']) }}

                        </div>


                        <!-- Recovery -->

                        <div class="mt-6">

                            <div class="text-xs text-gray-500">
                                Recovery Rate
                            </div>

                            <div class="text-2xl font-bold text-[#063a2a]">

                                {{ number_format($recoveryRate, 1) }}%

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- RECENT ACTIVITY -->
            <!-- ===================================================== -->

            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                <div class="flex items-center justify-between">

                    <h3 class="text-lg font-semibold text-[#063a2a]">
                        Recent Group Activity
                    </h3>

                    <a
                        href="#"
                        class="text-sm text-emerald-600"
                    >
                        View All →
                    </a>

                </div>


                <div class="mt-4 divide-y">

                    @php

                        $activities = collect();


                        /*
                        |--------------------------------------------------------------------------
                        | RECENT JOIN REQUESTS
                        |--------------------------------------------------------------------------
                        */

                        $group
                            ->joinRequests()
                            ->latest()
                            ->take(3)
                            ->get()
                            ->each(function ($r) use (&$activities) {

                                $activities->push([

                                    'member' =>
                                        $r->user->name,

                                    'type' =>
                                        'Join Request',

                                    'amount' =>
                                        null,

                                    'status' =>
                                        ucfirst($r->status),

                                    'date' =>
                                        $r->created_at
                                            ->toDateString(),

                                ]);

                            });


                        /*
                        |--------------------------------------------------------------------------
                        | TOP BORROWERS
                        |--------------------------------------------------------------------------
                        */

                        $topBorrowers
                            ->take(3)
                            ->each(function ($b) use (&$activities) {

                                $activities->push([

                                    'member' =>
                                        $b->user->name,

                                    'type' =>
                                        'Loan',

                                    'amount' =>
                                        $b->total_borrowed,

                                    'status' =>
                                        'Loan',

                                    'date' =>
                                        null,

                                ]);

                            });

                    @endphp


                    @if($activities->isEmpty())

                        <div class="py-6 text-gray-500">
                            No recent activity.
                        </div>

                    @else

                        @foreach($activities as $act)

                            <div class="py-3 flex items-center justify-between">

                                <div>

                                    <div class="font-semibold text-sm">
                                        {{ $act['member'] }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $act['type'] }}
                                    </div>

                                </div>


                                <div class="text-right">

                                    <div class="text-sm text-gray-500">

                                        @if($act['amount'])

                                            <span class="text-red-600 font-semibold">

                                                KES
                                                {{ number_format($act['amount']) }}

                                            </span>

                                        @else

                                            —

                                        @endif

                                    </div>


                                    <div class="text-xs mt-1">

                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs
                                            bg-green-100 text-green-700"
                                        >
                                            {{ $act['status'] }}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @endif

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- LOAN STATISTICS -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">


            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">


                <!-- Principal -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Principal Loaned
                    </h4>

                    <div class="mt-3 text-2xl font-bold text-blue-600">

                        KES
                        {{ number_format($loanStats['total_loaned']) }}

                    </div>

                </div>


                <!-- Interest -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Interest
                    </h4>

                    <div class="mt-3 text-2xl font-bold text-purple-600">

                        KES
                        {{ number_format($loanStats['total_interest']) }}

                    </div>

                </div>


                <!-- Total Payable -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Total Payable
                    </h4>

                    <div class="mt-3 text-2xl font-bold text-[#063a2a]">

                        KES
                        {{ number_format($loanStats['total_payable']) }}

                    </div>

                </div>


                <!-- Total Repaid -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Total Repaid
                    </h4>

                    <div class="mt-3 text-2xl font-bold text-green-600">

                        KES
                        {{ number_format($loanStats['total_repaid']) }}

                    </div>

                </div>


                <!-- Outstanding -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Outstanding
                    </h4>

                    <div
                        class="mt-3 text-2xl font-bold
                        {{ $loanStats['outstanding'] > 0
                            ? 'text-orange-600'
                            : 'text-green-600'
                        }}"
                    >

                        KES
                        {{ number_format($loanStats['outstanding']) }}

                    </div>

                </div>


                <!-- Recovery Rate -->

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <h4 class="text-gray-500">
                        Recovery Rate
                    </h4>

                    <div class="mt-3 text-2xl font-bold text-purple-600">

                        {{ number_format($recoveryRate, 1) }}%

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- CHAIRPERSON CONTROL ROOM -->
            <!-- ===================================================== -->

            @if($group->isChairperson())

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">

                    <div class="flex items-center justify-between">

                        <h3 class="font-semibold text-[#063a2a]">
                            Chairperson Control Room
                        </h3>

                        <span
                            class="text-xs text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full"
                        >
                            Admin Controls Active
                        </span>

                    </div>


                    <p class="text-sm text-gray-500 mt-2">

                        Update interest rates and grace periods
                        for this group.

                    </p>


                    <form
                        method="POST"
                        action="{{ route('groups.settings.update', $group->id) }}"
                        class="mt-4 space-y-3"
                    >

                        @csrf


                        <div>

                            <label class="text-xs text-gray-600">
                                Loan Interest Rate (%)
                            </label>

                            <input
                                name="interest_rate"
                                type="number"
                                step="0.01"
                                min="0"
                                value="{{ old(
                                    'interest_rate',
                                    $group->interest_rate ?? 5
                                ) }}"
                                class="mt-1 w-full border border-green-50 rounded px-3 py-2"
                            />

                        </div>


                        <div>

                            <label class="text-xs text-gray-600">
                                Grace Period (days)
                            </label>

                            <input
                                name="grace_period"
                                type="number"
                                min="0"
                                value="{{ old(
                                    'grace_period',
                                    $group->grace_period ?? 14
                                ) }}"
                                class="mt-1 w-full border border-green-50 rounded px-3 py-2"
                            />

                        </div>


                        <button
                            type="submit"
                            class="w-full mt-2 bg-emerald-600 text-white py-2 rounded"
                        >
                            Approve Settings Override
                        </button>

                    </form>

                </div>

            @endif

        </div>


        <!-- ========================================================= -->
        <!-- MEMBERSHIP APPROVAL -->
        <!-- ========================================================= -->

        @if($group->isChairperson())

            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50 mt-6">

                <div class="flex items-center justify-between mb-4">

                    <h3 class="font-semibold text-[#063a2a]">
                        Membership Approval Queue
                    </h3>

                    <span
                        class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full"
                    >
                        {{ $pendingRequests->count() }} Pending
                    </span>

                </div>


                @forelse($pendingRequests as $request)

                    <div class="border-b py-4 flex justify-between items-center">

                        <div>

                            <h4 class="font-semibold">
                                {{ $request->user->name }}
                            </h4>

                            <p class="text-sm text-gray-500">
                                {{ $request->user->email }}
                            </p>

                            @if($request->message)

                                <p class="text-sm mt-2 italic text-gray-600">

                                    "{{ $request->message }}"

                                </p>

                            @endif

                        </div>


                        <div class="flex gap-2">

                            <form
                                action="{{ route(
                                    'join-requests.approve',
                                    $request
                                ) }}"
                                method="POST"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg"
                                >
                                    Approve
                                </button>

                            </form>


                            <form
                                action="{{ route(
                                    'join-requests.reject',
                                    $request
                                ) }}"
                                method="POST"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg"
                                >
                                    Reject
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="py-8 text-center text-gray-500">

                        No pending membership requests.

                    </div>

                @endforelse

            </div>

        @endif


        <!-- ========================================================= -->
        <!-- MONTHLY LOAN DISBURSEMENTS -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50 mt-6">

            <div class="flex items-center justify-between">

                <div>

                    <h3 class="font-semibold text-[#063a2a]">
                        Monthly Loan Disbursements
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Principal loaned over time
                    </p>

                </div>

                <span class="text-sm text-gray-500">
                    KES
                </span>

            </div>


            <div class="mt-4">

                <canvas id="loanChart"></canvas>

            </div>

        </div>

    </main>


    <!-- ============================================================= -->
    <!-- CHART.JS -->
    <!-- ============================================================= -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>

        document.addEventListener('DOMContentLoaded', function () {


            /*
            |--------------------------------------------------------------------------
            | MONTHLY CONTRIBUTIONS
            |--------------------------------------------------------------------------
            */

            const monthlyContribElement =
    document.getElementById('monthlyContribChart');

if (monthlyContribElement) {

    const contributionLabels = @json($dailyContribLabels);
const contributionData = @json($dailyContribData);

    new Chart(monthlyContribElement, {

        type: 'line',

        data: {
            labels: contributionLabels,

            datasets: [{
                label: 'Monthly Contributions',
                data: contributionData,

                borderWidth: 3,

                tension: 0.4,

                fill: true,

                pointRadius: 0,

                pointHoverRadius: 7,

                pointBorderWidth: 3,

                pointBackgroundColor: '#ffffff',

                borderColor: '#059669',

                backgroundColor: 'rgba(5, 150, 105, 0.10)',

            }]
        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                intersect: false,
                mode: 'index'
            },

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    backgroundColor: '#063a2a',

                    titleColor: '#ffffff',

                    bodyColor: '#d1fae5',

                    padding: 14,

                    cornerRadius: 10,

                    displayColors: false,

                    callbacks: {

                        title: function(context) {
                            return context[0].label;
                        },

                        label: function(context) {

                            return 'KES ' +
                                new Intl.NumberFormat(
                                    'en-KE'
                                ).format(context.raw);

                        }

                    }

                }

            },

            scales: {

                x: {

    grid: {
        display: false
    },

    border: {
        display: false
    },

    ticks: {

        color: '#6b7280',

        font: {
            size: 10
        },

        maxRotation: 0,

        autoSkip: true,

        maxTicksLimit: 10

    }

},

                y: {

                    beginAtZero: true,

                    border: {
                        display: false
                    },

                    grid: {

                        color: 'rgba(0,0,0,0.05)',

                        drawTicks: false

                    },

                    ticks: {

                        color: '#6b7280',

                        padding: 10,

                        font: {
                            size: 11
                        },

                        callback: function(value) {

                            if (value >= 1000) {

                                return 'KES ' +
                                    (value / 1000) +
                                    'K';

                            }

                            return 'KES ' + value;

                        }

                    }

                }

            }

        }

    });

}


            /*
            |--------------------------------------------------------------------------
            | TOP CONTRIBUTORS
            |--------------------------------------------------------------------------
            */

            const contributorsElement =
                document.getElementById('contributorsChart');


            if (contributorsElement) {

                new Chart(
                    contributorsElement,
                    {

                        type: 'bar',

                        data: {

                            labels: @json($contributorsLabels),

                            datasets: [

                                {

                                    label: 'Contributions',

                                    data: @json($contributorsData),

                                    borderWidth: 1,

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {
                                    display: false
                                }

                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        callback: function(value) {

                                            return 'KES ' +
                                                new Intl.NumberFormat()
                                                    .format(value);

                                        }

                                    }

                                }

                            }

                        }

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | LOAN HEALTH DONUT
            |--------------------------------------------------------------------------
            |
            | Shows:
            |
            | Principal
            | Interest
            |
            */

            const donutElement =
                document.getElementById('donutHealth');


            if (donutElement) {

                new Chart(
                    donutElement,
                    {

                        type: 'doughnut',

                        data: {

                            labels: [

                                'Principal',

                                'Interest'

                            ],

                            datasets: [

                                {

                                    data: [

                                        {{ $loanStats['total_loaned'] }},

                                        {{ $loanStats['total_interest'] }}

                                    ],

                                    backgroundColor: [

                                        '#059669',

                                        '#8b5cf6'

                                    ],

                                    borderWidth: 0

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            cutout: '68%',

                            plugins: {

                                legend: {

                                    display: true,

                                    position: 'bottom'

                                },

                                tooltip: {

                                    callbacks: {

                                        label: function(context) {

                                            const value =
                                                context.raw || 0;

                                            return 'KES ' +
                                                new Intl.NumberFormat()
                                                    .format(value);

                                        }

                                    }

                                }

                            }

                        }

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | MONTHLY LOANS
            |--------------------------------------------------------------------------
            */

            const loanChartElement =
                document.getElementById('loanChart');


            if (loanChartElement) {

                new Chart(
                    loanChartElement,
                    {

                        type: 'bar',

                        data: {

                            labels: @json($loanChartLabels),

                            datasets: [

                                {

                                    label: 'Loans Issued',

                                    data: @json($loanChartData),

                                    borderWidth: 1,

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    display: false

                                }

                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        callback: function(value) {

                                            return 'KES ' +
                                                new Intl.NumberFormat()
                                                    .format(value);

                                        }

                                    }

                                }

                            }

                        }

                    }

                );

            }

        });

    </script>

</x-layouts.group>

