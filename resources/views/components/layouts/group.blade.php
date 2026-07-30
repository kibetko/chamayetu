@props([
    'group',
    'groups' => collect(),
])

<x-app-layout>

<div class="flex h-screen bg-[#D9E3F4] ">

    {{-- Mobile Overlay --}}
    <div
        id="overlay"
        class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside
        id="sidebar"
        class="fixed lg:relative z-50 flex h-full w-64 flex-col bg-gradient-to-b from-[#F7FBF8] to-[#F1F7F2] text-[#0f3a2b] transform -translate-x-full lg:translate-x-0 transition-all duration-300 shadow-sm border-r border-green-50">

        {{-- LOGO / GROUP SWITCHER HEADER --}}
        <div class="p-4 border-b border-green-50">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 shadow">
                        <span class="text-white font-bold">C</span>
                    </div>

                    <div>
                        <span class="text-sm font-semibold">
                            Chama<span class="text-emerald-600">Yetu</span>
                        </span>
                        <p class="text-[11px] text-green-600/60 mt-0.5">
                            Smart Savings
                        </p>
                    </div>
                </div>

                
            </div>
        </div>

       {{-- GROUP DROPDOWN --}}
@if($groups->count())
<div class="px-4 py-3 relative z-50">

    <div class="relative rounded-lg bg-white/60 border border-green-50 p-2">

        {{-- Trigger --}}
        <button
            id="groupDropdownBtn"
            type="button"
            class="w-full text-left flex items-center justify-between px-3 py-2 rounded-md bg-white text-sm font-medium shadow-sm hover:bg-gray-50 transition">

            <div>
                <p class="text-xs text-green-600/70">
                    Active Group
                </p>

                <p class="text-sm font-semibold text-[#063a2a]">
                    {{ $group->name }}
                </p>
            </div>

            <svg
                id="groupDropdownIcon"
                class="w-4 h-4 text-green-600 transition-transform duration-200"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24">

                <path d="M6 9l6 6 6-6" />

            </svg>

        </button>

        {{-- Floating Dropdown --}}
        <div
            id="groupDropdownMenu"
            class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-xl shadow-2xl overflow-hidden z-[999]">

            {{-- Groups --}}
            @foreach($groups as $g)
                <a
                    href="{{ route('groups.switch', $g->id) }}"
                    class="flex items-center justify-between px-4 py-3 text-sm hover:bg-emerald-50 transition">

                    <span>{{ $g->name }}</span>

                    @if($g->id === $group->id)
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">
                            Active
                        </span>
                    @endif

                </a>
            @endforeach

            <div class="border-t border-gray-100"></div>

            {{-- Actions --}}
            <div class="p-2 space-y-2">

                <a
                    href="{{ route('groups.create') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition">

                    <span>➕</span>
                    <span>Create Group</span>

                </a>

                <a
                    href="{{ route('groups.create') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-md border border-emerald-200 text-emerald-700 text-sm hover:bg-emerald-50 transition">

                    <span>👤</span>
                    <span>Add Member</span>

                </a>

            </div>

        </div>

    </div>

</div>
@endif

        {{-- NAVIGATION --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-auto">
            @php
                $navBase = "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors";
            @endphp

            <a href="{{ route('dashboard') }}"
               class="{{ $navBase }}
               {{ request()->routeIs('dashboard')
                    ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm'
                    : 'text-green-700 hover:bg-emerald-50' }}">
                <svg class="w-4 h-4 text-emerald-600"><!-- icon --></svg>
                Dashboard
            </a>

            <a href="{{ route('payments.index') }}"
               class="{{ $navBase }}
               {{ request()->routeIs('payments.*')
                    ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm'
                    : 'text-green-700 hover:bg-emerald-50' }}">
                <svg class="w-4 h-4 text-emerald-600"><!-- icon --></svg>
                Payments
            </a>

            <a href="{{ route('loans.index') }}"
               class="{{ $navBase }}
               {{ request()->routeIs('loans.*')
                    ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm'
                    : 'text-green-700 hover:bg-emerald-50' }}">
                <svg class="w-4 h-4 text-emerald-600"><!-- icon --></svg>
                Loans
            </a>

            <a href="{{ route('members.index') }}"
               class="{{ $navBase }}
               {{ request()->routeIs('members.*')
                    ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm'
                    : 'text-green-700 hover:bg-emerald-50' }}">
                <svg class="w-4 h-4 text-emerald-600"><!-- icon --></svg>
                Members
            </a>

            @if($group->isChairperson())
            <a href="{{ route('groups.settings') }}"
               class="{{ $navBase }}
               {{ request()->routeIs('groups.settings')
                    ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 shadow-sm'
                    : 'text-green-700 hover:bg-emerald-50' }}">
                <svg class="w-4 h-4 text-emerald-600"><!-- icon --></svg>
                Settings
            </a>
            @endif
        </nav>

        {{-- CTA --}}
        <div class="px-4 py-4">
            <a href="#"
               class="block w-full rounded-md bg-emerald-600 py-2 text-center text-sm font-semibold text-white shadow hover:bg-emerald-700 transition">
                + Apply for Loan
            </a>
        </div>

        {{-- FOOTER / PROFILE --}}
        <div class="px-4 pb-6">
            <a href="{{ route('help-center') }}" class="block px-3 py-2 text-sm text-green-700 rounded-md hover:bg-emerald-50">
                Help Center
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left mt-2 px-3 py-2 text-sm text-green-700 rounded-md hover:bg-emerald-50">
                    Logout
                </button>
            </form>

            
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 overflow-y-auto">

        {{-- MOBILE HEADER --}}
        <div class="flex items-center gap-3 bg-white p-4 shadow lg:hidden">

            <button id="menuButton" class="text-xl">
                ☰
            </button>

            <span class="font-semibold text-[#14263A]">
                ChamaYetu
            </span>

        </div>

        {{-- PAGE CONTENT --}}
        <div class="p-3 sm:p-4 lg:p-6">

            {{ $slot }}

        </div>

    </main>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const button = document.getElementById('menuButton');

    const dropdownBtn = document.getElementById('groupDropdownBtn');
    const dropdownMenu = document.getElementById('groupDropdownMenu');
    const dropdownIcon = document.getElementById('groupDropdownIcon');

    // Mobile Sidebar
    button?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Group Dropdown
    dropdownBtn?.addEventListener('click', (e) => {
        e.stopPropagation();

        dropdownMenu.classList.toggle('hidden');
        dropdownIcon.classList.toggle('rotate-180');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (
            dropdownMenu &&
            !dropdownMenu.contains(e.target) &&
            !dropdownBtn.contains(e.target)
        ) {
            dropdownMenu.classList.add('hidden');
            dropdownIcon?.classList.remove('rotate-180');
        }
    });

});
</script>
@endpush

</x-app-layout>