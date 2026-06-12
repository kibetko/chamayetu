<x-app-layout>

<div class="min-h-screen bg-[#D9E3F4] p-6">


<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-8 py-8">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">

                    <svg class="w-8 h-8 text-white"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M17 20h5V4H2v16h5m10 0v-4a3 3 0 00-3-3H10a3 3 0 00-3 3v4m10 0H7m10-11a3 3 0 11-6 0 3 3 0 016 0zm-8 0a3 3 0 11-6 0 3 3 0 016 0z" />

                    </svg>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">
                        Create New Group
                    </h1>

                    <p class="text-blue-100 mt-1">
                        Create a savings and loan group and start inviting members.
                    </p>

                </div>

            </div>

        </div>

        {{-- Content --}}
        <div class="p-8">

            {{-- Info Box --}}
            <div class="mb-8 rounded-2xl border border-blue-100 bg-blue-50 p-5">

                <h3 class="font-semibold text-blue-800 mb-2">
                    Before You Create
                </h3>

                <ul class="space-y-2 text-sm text-slate-600">

                    <li>• Choose a clear group name.</li>

                    <li>• Create a unique code members will use to join.</li>

                    <li>• You will automatically become the Chairperson.</li>

                    <li>• Financial settings can be configured later.</li>

                </ul>

            </div>

            <form
                action="{{ route('groups.store') }}"
                method="POST">

                @csrf

                {{-- Group Name --}}
                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Group Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g Umoja Savings Group"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                    @error('name')
                        <p class="text-red-500 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Description --}}
                <div class="mb-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Group Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Briefly describe the purpose of this group..."
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>

                </div>

                {{-- Group Code --}}
                <div class="mb-8">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Unique Group Code
                    </label>

                    <input
                        type="text"
                        name="unique_code"
                        value="{{ old('unique_code') }}"
                        placeholder="e.g SKF001"
                        required
                        oninput="this.value = this.value.toUpperCase()"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                    @error('unique_code')
                        <p class="text-red-500 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="text-sm text-gray-500 mt-2">
                        Members will use this code to request access to the group.
                    </p>

                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-4 border-t pt-6">

                    <a
                        href="{{ route('dashboard') }}"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition">

                        Create Group

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


</div>

</x-app-layout>
