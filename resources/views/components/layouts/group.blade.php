@props([
    'group',
    'groups' => collect(),
])

<x-app-layout>
<div class="flex h-screen bg-[#D9E3F4]">

    {{-- Mobile Overlay --}}
    <div
        id="overlay"
        class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden">
    </div>

    {{-- Sidebar --}}
    <aside
        id="sidebar"
        class="fixed lg:relative z-50 flex h-full w-56 flex-col bg-[#14263A] text-white transform -translate-x-full lg:translate-x-0 transition-all duration-300">

        {{-- Logo --}}
        <div class="p-5 pb-4">
            <div class="flex items-center gap-3">

                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500">
                    <img
                        src="/images/logo-icon.png"
                        alt="Logo"
                        class="h-4 w-4">
                </div>

                <div>
                    <span class="text-lg font-bold">
                        Chama<span class="text-emerald-400">Yetu</span>
                    </span>

                    <p class="mt-0.5 text-[10px] text-[#8a9bb0]">
                        Active Period: 2024
                    </p>
                </div>

            </div>
        </div>

        {{-- Group Switcher --}}
        @if($groups->count())
            <div class="px-4 pb-4">

                <select
                    onchange="window.location.href=this.value"
                    class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-emerald-300">

                    @foreach($groups as $userGroup)

                        <option
                            value="{{ route('groups.switch', $userGroup->id) }}"
                            {{ $group->id == $userGroup->id ? 'selected' : '' }}>

                            {{ $userGroup->name }}

                        </option>

                    @endforeach

                </select>

            </div>
        @endif

        <div class="mx-4 border-t border-[#2A3F58]"></div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1">

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                {{ request()->routeIs('dashboard')
                    ? 'bg-[#2563EB] text-white font-semibold'
                    : 'text-[#b0c4d8] hover:bg-[#243550] hover:text-white' }}">

                Dashboard

            </a>

            <a
                href="#"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                {{ request()->routeIs('payments.*')
                    ? 'bg-[#2563EB] text-white font-semibold'
                    : 'text-[#b0c4d8] hover:bg-[#243550] hover:text-white' }}">

                Payments

            </a>

            <a
                href="#"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                {{ request()->routeIs('loans.*')
                    ? 'bg-[#2563EB] text-white font-semibold'
                    : 'text-[#b0c4d8] hover:bg-[#243550] hover:text-white' }}">

                Loans

            </a>

            <a
                href="{{ route('members.index') }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                {{ request()->routeIs('members.*')
                    ? 'bg-[#2563EB] text-white font-semibold'
                    : 'text-[#b0c4d8] hover:bg-[#243550] hover:text-white' }}">

                Members

            </a>

            <a
                href="{{ route('groups.settings') }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition
                {{ request()->routeIs('groups.settings')
                    ? 'bg-[#2563EB] text-white font-semibold'
                    : 'text-[#b0c4d8] hover:bg-[#243550] hover:text-white' }}">

                Settings

            </a>

        </nav>

        <div class="mx-4 border-t border-[#2A3F58]"></div>

        {{-- Loan Button --}}
        <div class="px-4 py-4">

            <a
                href="#"
                class="block w-full rounded-xl border border-emerald-500 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-500">

                Apply for Loan

            </a>

        </div>

        {{-- Footer --}}
        <div class="space-y-1 px-4 pb-6">

            <a
                href="#"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-[#b0c4d8] transition hover:bg-[#243550] hover:text-white">

                Help Center

            </a>

            <form
                method="POST"
                action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-[#b0c4d8] transition hover:bg-[#243550] hover:text-white">

                    Logout

                </button>

            </form>

        </div>

    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto bg-[#D9E3F4]">

        {{-- Mobile Header --}}
        <div class="flex items-center gap-3 bg-white p-4 shadow lg:hidden">

            <button id="menuButton">
                ☰
            </button>

            <span class="font-semibold text-[#14263A]">
                ChamaYetu
            </span>

        </div>

        {{-- Page Content --}}
        <div class="p-6">

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

    button?.addEventListener('click', () => {

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');

    });

    overlay?.addEventListener('click', () => {

        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');

    });

});
</script>
@endpush

</x-app-layout>