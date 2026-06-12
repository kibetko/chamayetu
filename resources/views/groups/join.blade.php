<x-app-layout>

<div class="min-h-screen bg-[#D9E3F4] p-6">


<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-500">

            <h1 class="text-3xl font-bold text-white">
                Request To Join Group
            </h1>

            <p class="text-blue-100 mt-2">
                Enter a valid group code and send a request to join.
            </p>

        </div>

        {{-- Success Message --}}
        @if(session('success'))

            <div class="mx-8 mt-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl">

                {{ session('success') }}

            </div>

        @endif

        {{-- Form --}}
        <form
            action="{{ route('groups.join.submit') }}"
            method="POST"
            class="p-8">

            @csrf

            {{-- Group Code --}}
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Group Code
                </label>

                <input
                    type="text"
                    name="group_code"
                    value="{{ old('group_code') }}"
                    placeholder="Enter group code"
                    required
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                @error('group_code')
                    <p class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Message --}}
            <div class="mb-8">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message (Optional)
                </label>

                <textarea
                    name="message"
                    rows="4"
                    placeholder="Introduce yourself or leave a message for the group leaders..."
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('message') }}</textarea>

                @error('message')
                    <p class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Information Box --}}
            <div class="mb-8 rounded-xl bg-blue-50 border border-blue-100 p-4">

                <h3 class="font-semibold text-blue-800 mb-2">
                    What happens next?
                </h3>

                <ul class="text-sm text-slate-600 space-y-1">
                    <li>• Your request will be sent to the group chairperson.</li>
                    <li>• The group leadership can approve or reject your request.</li>
                    <li>• Once approved, you'll gain access to the group's features.</li>
                </ul>

            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-4">

                <a
                    href="{{ route('dashboard') }}"
                    class="px-6 py-3 rounded-xl border border-gray-300 hover:bg-gray-50">

                    Cancel

                </a>

                <button
                    type="submit"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition">

                    Submit Request

                </button>

            </div>

        </form>

    </div>

</div>


</div>

</x-app-layout>
