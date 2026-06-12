<x-app-layout>
    <div class="min-h-screen bg-[#D9E3F4] py-10">

        <div class="max-w-5xl mx-auto px-6">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-800">
                    My Profile
                </h1>

                <p class="text-slate-500 mt-1">
                    Manage your account details and security settings.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">

                {{-- LEFT CARD (SUMMARY) --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">

                    <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold mb-4">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <h2 class="text-xl font-bold text-slate-800">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="text-slate-500 text-sm mt-1">
                        {{ auth()->user()->email }}
                    </p>

                    <p class="text-slate-500 text-sm mt-1">
                        {{ auth()->user()->phone_no ?? 'No phone added' }}
                    </p>

                    <div class="mt-6 border-t pt-4 text-sm text-slate-600">

                        <p>
                            <span class="font-semibold">Member since:</span>
                            {{ auth()->user()->created_at->format('M Y') }}
                        </p>

                        <p class="mt-2">
                            <span class="font-semibold">Groups:</span>
                            {{ auth()->user()->groups->count() }}
                        </p>

                    </div>

                </div>

                {{-- RIGHT SIDE FORMS --}}
                <div class="md:col-span-2 space-y-6">

                    {{-- UPDATE INFO --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">

                        <h2 class="text-lg font-semibold mb-4">
                            Update Profile
                        </h2>

                        @include('profile.partials.update-profile-information-form')

                    </div>

                    {{-- PASSWORD --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">

                        <h2 class="text-lg font-semibold mb-4">
                            Change Password
                        </h2>

                        @include('profile.partials.update-password-form')

                    </div>

                    {{-- DELETE ACCOUNT --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-200">

                        <h2 class="text-lg font-semibold text-red-600 mb-4">
                            Danger Zone
                        </h2>

                        @include('profile.partials.delete-user-form')

                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>