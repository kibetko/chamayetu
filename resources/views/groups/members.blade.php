```blade
<x-app-layout>

<div class="p-6">

    <div class="bg-white rounded-xl shadow">

        <div class="p-6 border-b">

            <h1 class="text-2xl font-bold">
                Group Members
            </h1>

            <p class="text-gray-500 mt-1">
                Members of {{ $group->name }}
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Name
                        </th>

                        <th class="text-left px-6 py-4">
                            Email
                        </th>

                        <th class="text-left px-6 py-4">
                            Role
                        </th>

                        <th class="text-left px-6 py-4">
                            Joined
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($group->members as $member)

<tr class="border-t">

    <td class="px-6 py-4">
        <p class="font-semibold">
            {{ $member->name }}
        </p>
    </td>

    <td class="px-6 py-4 text-gray-600">
        {{ $member->email }}
    </td>

    <td class="px-6 py-4">

        @if($member->pivot->role === 'chairperson')

            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                Chairperson
            </span>

        @else

            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                Member
            </span>

        @endif

    </td>

    <td class="px-6 py-4 text-gray-600">
        {{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y') }}
    </td>

</tr>

@empty

<tr>
    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
        No members found.
    </td>
</tr>

@endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-app-layout>
```
