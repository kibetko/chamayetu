<x-app-layout>

<div class="max-w-6xl mx-auto p-6">

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold">
            Available Groups
        </h1>

        <a
            href="{{ route('groups.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded">

            Create Group

        </a>

    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($groups as $group)

            <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-xl font-bold mb-2">
                    {{ $group->name }}
                </h2>

                <p class="text-gray-600 mb-4">
                    {{ $group->description }}
                </p>

                <p class="text-sm text-gray-500 mb-4">
                    Code: {{ $group->unique_code }}
                </p>

                <a
                    href="{{ route('groups.join') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded">

                    Request Join

                </a>

            </div>

        @endforeach

    </div>

</div>

</x-app-layout>