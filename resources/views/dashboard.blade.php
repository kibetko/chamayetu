<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ChamaYetu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">

        <!-- Logo -->
        <div class="p-6 border-b border-blue-700">
            <h1 class="text-2xl font-bold">ChamaYetu</h1>
        </div>

        <!-- Group Switcher -->
        <div class="p-4 border-b border-blue-700">
            <select class="w-full p-2 rounded text-black">
                <option>My Savings Group</option>
                <option>Family Chama</option>
                <option>Business Circle</option>
            </select>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
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
                Reports
            </a>

            <a href="#" class="block px-4 py-3 rounded hover:bg-blue-700">
                Settings
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-blue-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-red-500 py-2 rounded hover:bg-red-600">
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">

        <!-- Top Header -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Dashboard
            </h2>
            <p class="text-gray-600">
                Welcome back to your group management system
            </p>
        </div>

        <!-- Placeholder Cards -->
        <div class="grid grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold">Total Members</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">24</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold">Monthly Contributions</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">KES 120,000</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-lg font-semibold">Active Loans</h3>
                <p class="text-3xl font-bold text-orange-600 mt-2">8</p>
            </div>

        </div>

    </main>

</div>

</body>
</html>