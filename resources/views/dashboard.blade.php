

    <x-app-layout>

<div class="flex h-screen bg-gray-100">

```
<!-- Mobile Overlay -->
<div
    id="overlay"
    class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
</div>

<!-- Sidebar -->
<aside
    id="sidebar"
    class="fixed lg:relative z-50 w-72 h-full bg-blue-900 text-white transform -translate-x-full lg:translate-x-0 transition-all duration-300">

    <!-- Logo -->
    <div class="p-6 border-b border-blue-800">

        <h1 class="text-2xl font-bold">
            ChamaYetu
        </h1>

    </div>

    <!-- Group Switcher -->
    <div class="p-4 border-b border-blue-800">

        <label class="block text-sm mb-2">
            Active Group
        </label>

        <select
            onchange="window.location.href=this.value"
            class="w-full rounded text-black p-2">

            @foreach($groups as $userGroup)

                <option
                    value="{{ route('groups.switch',$userGroup->id) }}"
                    {{ $group->id == $userGroup->id ? 'selected' : '' }}>

                    {{ $userGroup->name }}

                </option>

            @endforeach

        </select>

    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-2 flex-1">

        <a href="#" class="block px-4 py-3 rounded bg-blue-700">
            Dashboard
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Members
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Contributions
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Loans
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Join Requests
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Invitations
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Reports
        </a>

        <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
            Settings
        </a>

    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-blue-800">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                class="w-full bg-red-500 hover:bg-red-600 py-3 rounded">

                Logout

            </button>

        </form>

    </div>

</aside>

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

        <div>

            <span class="font-medium">

                {{ auth()->user()->name }}

            </span>

        </div>

    </div>

    <!-- Content -->
    <div class="p-6">

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <div class="bg-white rounded-xl shadow p-6">

                <h3 class="text-gray-500">
                    Members
                </h3>

                <p class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $stats['members'] }}
                </p>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <h3 class="text-gray-500">
                    Total Contributions
                </h3>

                <p class="text-3xl font-bold text-green-600 mt-2">

                    KES {{ number_format($stats['contributions']) }}

                </p>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <h3 class="text-gray-500">
                    Active Loans
                </h3>

                <p class="text-3xl font-bold text-orange-600 mt-2">

                    {{ $stats['active_loans'] }}

                </p>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <h3 class="text-gray-500">
                    Join Requests
                </h3>

                <p class="text-3xl font-bold text-red-600 mt-2">

                    {{ $stats['pending_requests'] }}

                </p>

            </div>

        </div>

        <!-- Activity + Requests -->
        <div class="grid lg:grid-cols-2 gap-6 mt-8">

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

            <div class="bg-white rounded-xl shadow">

                <div class="p-4 border-b">

                    <h3 class="font-bold">
                        Pending Requests
                    </h3>

                </div>

                <div class="p-4">

                    @forelse(
                        $group->joinRequests()
                            ->where('status','pending')
                            ->latest()
                            ->take(5)
                            ->get()

                        as $request
                    )

                        <div class="flex justify-between py-2">

                            <span>
                                {{ $request->user->name }}
                            </span>

                            <span class="text-yellow-600">
                                Pending
                            </span>

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
```

</div>

<script>

const sidebar =
document.getElementById('sidebar');

const overlay =
document.getElementById('overlay');

const button =
document.getElementById('menuButton');

button?.addEventListener('click', () => {

    sidebar.classList.toggle('-translate-x-full');

    overlay.classList.toggle('hidden');

});

overlay?.addEventListener('click', () => {

    sidebar.classList.add('-translate-x-full');

    overlay.classList.add('hidden');

});

</script>

</x-app-layout>


