<x-layouts.group
    :group="$group"
    :groups="$groups">

    <div class="bg-white rounded-xl shadow">

        <div class="p-6 border-b">

            <h1 class="text-2xl font-bold">
                Group Members
            </h1>

            <p class="text-gray-500">
                Members of {{ $group->name }}
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="text-left px-6 py-4">Name</th>
                        <th class="text-left px-6 py-4">Email</th>
                        <th class="text-left px-6 py-4">Phone</th>
                        <th class="text-left px-6 py-4">Role</th>
                        <th class="text-left px-6 py-4">Joined</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($group->members as $member)

                        <tr class="border-t">

                            <td class="px-6 py-4">
                                {{ $member->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $member->email }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $member->phone_no ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ ucfirst($member->pivot->role) }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $member->pivot->joined_at
                                    ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y')
                                    : '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-6 text-gray-500">
                                No members found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.group>