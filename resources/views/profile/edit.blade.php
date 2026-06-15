<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-[#F3F7F5] to-[#EAF6F0] py-10">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-800">My Profile</h1>
                    <p class="text-sm text-slate-600 mt-1">Manage your account details, security and group memberships.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('groups.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-full text-sm text-slate-700 hover:shadow">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"/></svg>
                        My Groups
                    </a>

                    <a href="{{ route('profile.update') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-full text-sm hover:bg-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">

                <!-- Profile card -->
                <aside class="lg:col-span-1 bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-emerald-600 text-white flex items-center justify-center text-3xl font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm text-slate-500 mt-1">{{ auth()->user()->email }}</div>
                            <div class="text-xs text-slate-400 mt-1">{{ auth()->user()->phone_no ?? 'No phone number' }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-lg bg-emerald-50 p-3">
                            <div class="text-xs text-slate-500">Groups</div>
                            <div class="font-semibold text-emerald-700 text-lg mt-1">{{ auth()->user()->groups->count() }}</div>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <div class="text-xs text-slate-500">Member since</div>
                            <div class="font-semibold text-slate-700 text-lg mt-1">{{ auth()->user()->created_at->format('M Y') }}</div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('groups.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-slate-100 hover:shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">🏘️</div>
                                <div>
                                    <div class="text-sm font-medium text-slate-800">View Groups</div>
                                    <div class="text-xs text-slate-400">Open your groups dashboard</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('profile.update') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-slate-100 hover:shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">🔒</div>
                                <div>
                                    <div class="text-sm font-medium text-slate-800">Security</div>
                                    <div class="text-xs text-slate-400">Change password & 2FA</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </aside>

                <!-- Main forms -->
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-slate-800">Profile Details</h2>
                        <p class="text-sm text-slate-500 mt-1">Update your display name, email and phone number.</p>

                        <div class="mt-4">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-slate-800">Security</h2>
                        <p class="text-sm text-slate-500 mt-1">Change your password and manage account security.</p>

                        <div class="mt-4">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-100">
                        <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
                        <p class="text-sm text-slate-500 mt-1">Permanently delete your account and data.</p>

                        <div class="mt-4">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>