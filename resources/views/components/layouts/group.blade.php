@props([
    'group',
    'groups'
])

<x-app-layout>

<div class="flex h-screen bg-gray-100">

    <!-- Overlay -->
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
        <nav class="p-4 space-y-2">

            <a
                href="{{ route('dashboard') }}"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Dashboard

            </a>

            <a
                href="{{ route('members.index') }}"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Members

            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Contributions

            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Loans

            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Join Requests

            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-blue-700">

                Reports

            </a>

            <a
                href="#"
                class="block px-4 py-3 rounded hover:bg-blue-700">

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

    <!-- Main Area -->
    <main class="flex-1 overflow-y-auto">

        <!-- Header -->
        <div class="bg-white shadow p-4 flex justify-between items-center">

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

            <span class="font-medium">
                {{ auth()->user()->name }}
            </span>

        </div>

        <!-- Dynamic Content -->
        <div class="p-6">

            {{ $slot }}

        </div>

    </main>

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