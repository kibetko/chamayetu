<x-app-layout>

<div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-xl shadow">

    <h1 class="text-3xl font-bold mb-6">
        Request To Join Group
    </h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form
        action="{{ route('groups.join.submit') }}"
        method="POST">

        @csrf

        <div class="mb-4">

            <label class="block mb-2">
                Group Code
            </label>

            <input
                type="text"
                name="group_code"
                class="w-full border rounded p-3"
                required>

        </div>

        

        <div class="mb-4">

            <label class="block mb-2">
                Message
            </label>

            <textarea
                name="message"
                rows="4"
                class="w-full border rounded p-3"></textarea>

        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-3 rounded">

            Submit Request

        </button>

    </form>

</div>

</x-app-layout>