<x-app-layout>

    <div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-xl shadow">

        <h1 class="text-3xl font-bold mb-6">
            Create New Group
        </h1>

        <form action="{{ route('groups.store') }}" method="POST">

            @csrf

            <div class="mb-4">
                <label class="block mb-2 font-medium">
                    Group Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border rounded p-3"
                    required>

                @error('name')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="w-full border rounded p-3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">
                    Unique Group Code
                </label>

                <input
                    type="text"
                    name="unique_code"
                    value="{{ old('unique_code') }}"
                    class="w-full border rounded p-3"
                    placeholder="e.g SKF001"
                    required>

                @error('unique_code')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

                <p class="text-gray-500 text-sm mt-1">
                    Members will use this code to request joining your group.
                </p>
            </div>

            <div class="mb-6">

                <label class="flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="allow_join_requests"
                        value="1"
                        checked>

                    <span>
                        Allow members to request joining via group code
                    </span>

                </label>

            </div>

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">

                Create Group

            </button>

        </form>

    </div>

</x-app-layout>