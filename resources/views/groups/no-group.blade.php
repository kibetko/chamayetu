<x-app-layout>

    <div class="min-h-[80vh] flex items-center justify-center">

        <div class="bg-white rounded-xl shadow-lg p-10 text-center max-w-lg">

            <h1 class="text-3xl font-bold mb-4">
                Welcome to ChamaYetu
            </h1>

            <p class="text-gray-600 mb-8">
                You have not joined any group yet.
            </p>

            <div class="flex justify-center gap-4">

                <a
                    href="{{ route('groups.create') }}"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg">

                    Create Group

                </a>

                <a
    href="{{ route('groups.index') }}"
    class="bg-green-600 text-white px-6 py-3 rounded-lg">

    Join Group

</a>

            </div>

        </div>

    </div>

</x-app-layout>