{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChamaYetu') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="flex flex-col bg-[#1B424C] text-white transition-all duration-300 w-64 min-h-screen">

        <!-- Logo -->
        <div class="flex items-center gap-2 px-5 py-5 border-b border-white/10">
            <span class="text-xl font-bold text-white sidebar-text">Chama</span>
            <span class="text-xl font-bold text-[#39C260] sidebar-text">Yetu</span>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-6 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span class="sidebar-text text-sm">Dashboard</span>
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('loans.*') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span class="sidebar-text text-sm">My Loans</span>
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('apply.*') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                <span class="sidebar-text text-sm">Apply for Loan</span>
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('payments.*') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                <span class="sidebar-text text-sm">Payments</span>
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('members.*') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="sidebar-text text-sm">Members</span>
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('profile.*') ? 'bg-white/15 font-semibold' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
                <span class="sidebar-text text-sm">Profile & Settings</span>
            </a>

        </nav>

        <!-- Logout -->
        <div class="px-3 py-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl transition-all hover:bg-white/10 text-left">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span class="sidebar-text text-sm">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- TOP NAR -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Collapse toggle -->
                <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>

            <!-- User info -->
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                <div class="w-9 h-9 rounded-full bg-[#1B424C] text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const texts = document.querySelectorAll('.sidebar-text');

    toggle.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.contains('w-16');
        if (isCollapsed) {
            sidebar.classList.replace('w-16', 'w-64');
            texts.forEach(t => t.classList.remove('hidden'));
        } else {
            sidebar.classList.replace('w-64', 'w-16');
            texts.forEach(t => t.classList.add('hidden'));
        }
    });
</script>

</body>
</html>