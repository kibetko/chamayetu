<section class="space-y-6">

    <header>
        <h2 class="text-lg font-semibold text-slate-800">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Update your account details and contact information.
        </p>
    </header>

    {{-- EMAIL VERIFICATION FORM --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- MAIN FORM --}}
    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">

        @csrf
        @method('patch')

        {{-- NAME --}}
        <div>
            <x-input-label for="name" value="Full Name" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full rounded-xl"
                :value="old('name', $user->name)"
                required
                autofocus
            />

            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" value="Email Address" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full rounded-xl"
                :value="old('email', $user->email)"
                required
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-red-600">
                    Your email is unverified.
                </p>
            @endif
        </div>

        {{-- PHONE NUMBER (NEW FIELD) --}}
        <div>
            <x-input-label for="phone_no" value="Phone Number" />

            <x-text-input
                id="phone_no"
                name="phone_no"
                type="text"
                class="mt-1 block w-full rounded-xl"
                :value="old('phone_no', $user->phone_no)"
                placeholder="e.g. 2547XXXXXXXX"
            />

            <x-input-error class="mt-2" :messages="$errors->get('phone_no')" />
        </div>

        {{-- BUTTON --}}
        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow">

                Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600">

                    Saved successfully.
                </p>
            @endif

        </div>

    </form>

</section>