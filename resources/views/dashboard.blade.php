<x-layouts.group
    :group="$group"
    :groups="$groups">

<div class="flex h-screen bg-[#F3F7F5]">

    <!-- Mobile Overlay -->
    <div
        id="overlay"
        class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
    </div>

    <!-- Main -->
    <main class="flex-1 overflow-y-auto p-6">

        <!-- Top header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#0f3a2b]">Dashboard Overview</h1>
                <p class="text-sm text-gray-600 mt-1">Welcome back, {{ Auth::user()->name }}</p>
            </div>

            {{-- Profile link --}}
            <div class="flex items-center gap-4">
                <div class="relative">

                    <button
                        onclick="document.getElementById('notification-dropdown').classList.toggle('hidden')"
                        class="relative">

                        🔔

                        @if(auth()->user()->unreadNotifications->count())
                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif

                    </button>
    <div class="relative">

   

    <div
        id="notification-dropdown"
        class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-lg border z-50">

        <div class="p-4 border-b">
            <h3 class="font-semibold">
                Notifications
            </h3>
        </div>

        <div class="max-h-80 overflow-y-auto">

            @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notification)

                <div class="p-4 border-b">

                    <div class="font-semibold text-sm">
                        {{ $notification->data['title'] ?? '' }}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        {{ $notification->data['message'] ?? '' }}
                    </div>

                    <div class="text-xs text-gray-400 mt-2">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>

                </div>

            @empty

                <div class="p-4 text-gray-500">
                    No notifications
                </div>

            @endforelse

        </div>

    </div>

</div>

</div>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-3 rounded-full px-3 py-1 hover:bg-white/50">
                    <div class="h-9 w-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    
                </a>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">
                <div class="text-xs text-gray-500">Total Group Contributions</div>
                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">
                    KES {{ number_format($stats['contributions']) }}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">
                <div class="text-xs text-gray-500">My Contribution Summary</div>
                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">
                    KES 0
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">
                <div class="text-xs text-gray-500">Total Loans Issued</div>
                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">
                    KES {{ number_format($loanStats['total_loaned']) }}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border border-green-50">
                <div class="text-xs text-gray-500">Outstanding Loan Balances</div>
                <div class="mt-3 text-2xl font-semibold text-[#063a2a]">
                    KES {{ number_format($loanStats['outstanding']) }}
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6">

    


</div>
        </div>

        {{-- Infographic: monthly contributions + top contributors --}}
        @php
            $monthlyContribLabels = isset($monthlyContributions) ? $monthlyContributions->pluck('month') : (isset($monthlyLoans) ? $monthlyLoans->pluck('month') : collect());
            $monthlyContribData = isset($monthlyContributions) ? $monthlyContributions->pluck('total') : (isset($monthlyLoans) ? $monthlyLoans->pluck('total') : collect());

            // prepare contributors: prefer $topContributors, fallback to topBorrowers or group members
            if (isset($topContributors)) {
                $contributors = $topContributors;
            } elseif (isset($topBorrowers)) {
                $contributors = $topBorrowers->map(function($b){ return (object)['name' => $b->user->name ?? 'Unknown', 'total' => $b->total_borrowed ?? 0]; });
            } elseif (isset($group) && $group->members) {
                $contributors = $group->members->map(function($m){ return (object)['name' => $m->name, 'total' => $m->contributions_sum ?? 0]; })->sortByDesc('total')->take(6);
            } else {
                $contributors = collect();
            }

            $contributorsLabels = $contributors->pluck('name');
            $contributorsData = $contributors->pluck('total');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#063a2a]">Monthly Contributions</h3>
                        <p class="text-sm text-gray-500 mt-1">Contributions over time</p>
                    </div>
                    <div class="text-sm text-gray-500">KES</div>
                </div>

                <div class="mt-4">
                    <canvas id="monthlyContribChart" style="max-height:220px;"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#063a2a]">Top Contributors</h3>
                        <p class="text-sm text-gray-500 mt-1">Contributions by member (recent)</p>
                    </div>
                    <div class="text-sm text-gray-500">KES</div>
                </div>

                <div class="mt-4">
                    <canvas id="contributorsChart" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Main content: loan health + recent activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

            <!-- Loan Health -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#063a2a]">Loan Health</h3>
                        <p class="text-sm text-gray-500 mt-1">Portfolio repayment overview</p>
                    </div>
                    <div class="text-sm text-gray-500">KES {{ number_format($loanStats['outstanding']) }}</div>
                </div>

                <div class="mt-6 flex items-center gap-6">
                    <div class="w-36 h-36">
                        <canvas id="donutHealth"></canvas>
                    </div>

                    <div class="flex-1">
                        <div class="text-sm text-gray-600">Active / Paid</div>
                        <div class="mt-4 text-sm text-gray-600">Defaulted</div>

                        <div class="mt-6">
                            <div class="text-xs text-gray-500">Healthy</div>
                            <div class="text-2xl font-bold text-[#063a2a]">{{ number_format($recoveryRate,1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Group Activity -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#063a2a]">Recent Group Activity</h3>
                    <a href="#" class="text-sm text-emerald-600">View All →</a>
                </div>

                <div class="mt-4 divide-y">
                    @php
                        // Fallback activity using recent join requests and top borrowers
                        $activities = collect();

                        // recent join requests (if any)
                        $group->joinRequests()->latest()->take(3)->get()->each(function($r) use (&$activities){
                            $activities->push([
                                'member' => $r->user->name,
                                'type' => 'Join Request',
                                'amount' => null,
                                'status' => ucfirst($r->status),
                                'date' => $r->created_at->toDateString(),
                            ]);
                        });

                        // top borrowers as loan requests placeholder
                        $topBorrowers->take(3)->each(function($b) use (&$activities){
                            $activities->push([
                                'member' => $b->user->name,
                                'type' => 'Loan',
                                'amount' => $b->total_borrowed,
                                'status' => 'Completed',
                                'date' => null,
                            ]);
                        });
                    @endphp

                    @if($activities->isEmpty())
                        <div class="py-6 text-gray-500">No recent activity.</div>
                    @else
                        @foreach($activities as $act)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-sm">{{ $act['member'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $act['type'] }}</div>
                                </div>

                                <div class="text-right">
                                    <div class="text-sm text-gray-500">
                                        @if($act['amount'])
                                            <span class="text-red-600 font-semibold">KES {{ number_format($act['amount']) }}</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="text-xs mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-{{ strtolower($act['status']) === 'pending' ? 'yellow' : 'green' }}-100 text-{{ strtolower($act['status']) === 'pending' ? 'yellow' : 'green' }}-700">
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

        <!-- Lower controls: stats grid + chairperson control room -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                    <h4 class="text-gray-500">Total Loaned</h4>
                    <div class="mt-3 text-2xl font-bold text-blue-600">KES {{ number_format($loanStats['total_loaned']) }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                    <h4 class="text-gray-500">Total Repaid</h4>
                    <div class="mt-3 text-2xl font-bold text-green-600">KES {{ number_format($loanStats['total_repaid']) }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                    <h4 class="text-gray-500">Outstanding</h4>
                    <div class="mt-3 text-2xl font-bold text-orange-600">KES {{ number_format($loanStats['outstanding']) }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                    <h4 class="text-gray-500">Recovery Rate</h4>
                    <div class="mt-3 text-2xl font-bold text-purple-600">{{ number_format($recoveryRate,1) }}%</div>
                </div>
            </div>

            @if($group->isChairperson())
            <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-[#063a2a]">Chairperson Control Room</h3>
                    <span class="text-xs text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full">Admin Controls Active</span>
                </div>

                <p class="text-sm text-gray-500 mt-2">Update interest rates and grace periods for this group.</p>

                <form method="POST" action="{{ route('groups.settings.update', $group->id) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-600">Loan Interest Rate (%)</label>
                        <input name="interest_rate" value="{{ old('interest_rate', $group->interest_rate ?? 5) }}" class="mt-1 w-full border border-green-50 rounded px-3 py-2" />
                    </div>

                    <div>
                        <label class="text-xs text-gray-600">Grace Period (days)</label>
                        <input name="grace_period" value="{{ old('grace_period', $group->grace_period ?? 14) }}" class="mt-1 w-full border border-green-50 rounded px-3 py-2" />
                    </div>

                    <button type="submit" class="w-full mt-2 bg-emerald-600 text-white py-2 rounded">Approve Settings Override</button>
                </form>
            </div>
            @endif

            @if($group->isChairperson())

<div class="bg-white rounded-xl shadow-sm p-6 border border-green-50 mt-6">

    <div class="flex items-center justify-between mb-4">

        <h3 class="font-semibold text-[#063a2a]">
            Membership Approval Queue
        </h3>

        <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
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
                    action="{{ route('join-requests.approve', $request) }}"
                    method="POST">

                    @csrf

                    <button
                        class="bg-green-600 text-white px-4 py-2 rounded-lg">

                        Approve

                    </button>

                </form>

                <form
                    action="{{ route('join-requests.reject', $request) }}"
                    method="POST">

                    @csrf

                    <button
                        class="bg-red-600 text-white px-4 py-2 rounded-lg">

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

        </div>

        <!-- Chart area -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-green-50 mt-6">
            <h3 class="font-semibold text-[#063a2a]">Monthly Loan Disbursements</h3>
            <div class="mt-4">
                <canvas id="loanChart"></canvas>
            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Donut (loan health) uses recoveryRate
    const donutCtx = document.getElementById('donutHealth').getContext('2d');
    const healthy = Number(@json($recoveryRate)) || 0;
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Healthy', 'At Risk'],
            datasets: [{
                data: [healthy, Math.max(0, 100 - healthy)],
                backgroundColor: ['#10B981', '#E5E7EB'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false },
            }
        }
    });

    // Loan line chart
    const ctx = document.getElementById('loanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyLoans->pluck('month')),
            datasets: [{
                label: 'Loan Amount',
                data: @json($monthlyLoans->pluck('total')),
                borderWidth: 3,
                borderColor: '#16A34A',
                backgroundColor: 'rgba(16,163,74,0.08)',
                tension: 0.35,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Monthly contributions chart (bar)
    const monthlyCtx = document.getElementById('monthlyContribChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyContribLabels),
                datasets: [{
                    label: 'Contributions',
                    data: @json($monthlyContribData),
                    backgroundColor: '#10B981',
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Top contributors (horizontal bar)
    const contribCtx = document.getElementById('contributorsChart')?.getContext('2d');
    if (contribCtx) {
        new Chart(contribCtx, {
            type: 'bar',
            data: {
                labels: @json($contributorsLabels),
                datasets: [{
                    label: 'Contributed',
                    data: @json($contributorsData),
                    backgroundColor: ['#60A5FA','#34D399','#F472B6','#F59E0B','#A78BFA','#F97316']
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

</x-layouts.group>