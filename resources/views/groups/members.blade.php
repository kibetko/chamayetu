<x-layouts.group
    :group="$group"
    :groups="$groups">

    <div class="rounded-2xl bg-[#D9E3F4] p-6">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Group Members
                </h1>

                <p class="mt-1 text-sm text-slate-600">
                    Manage and view all registered members.
                </p>
            </div>

            <!-- Search -->
            <div class="relative w-full md:w-80">

                <input
                    type="text"
                    id="memberSearch"
                    placeholder="Search member..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-10 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">

                <svg
                    class="absolute left-3 top-3.5 h-5 w-5 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>

                </svg>

            </div>

        </div>

        <!-- Members Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr class="bg-slate-800 text-white">

                            <th class="px-6 py-4 text-left text-sm font-semibold">
                                Name
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold">
                                Email
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold">
                                Phone
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold">
                                Role
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold">
                                Joined
                            </th>

                        </tr>

                    </thead>

                    <tbody id="membersTable">
                        @php
                        $members = $group->members->sortBy(function ($member) {
                            return match ($member->pivot->role) {
                                'chairperson' => 1,
                                'secretary' => 2,
                                'treasurer' => 3,
                                default => 4,
                            };
                        });
                        @endphp

                        @forelse($members as $member)

                     

                            <tr class="member-row border-b hover:bg-slate-50 transition">

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-semibold text-emerald-700">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>

                                        <span class="member-name font-medium text-slate-800">
                                            {{ $member->name }}
                                        </span>

                                    </div>

                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $member->email }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $member->phone_no ?? '-' }}
                                </td>

                                <td class="px-6 py-4">

                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ ucfirst($member->pivot->role) }}
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $member->pivot->joined_at
                                        ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y')
                                        : '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="py-10 text-center text-slate-500">
                                    No members found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const search = document.getElementById('memberSearch');
            const rows = document.querySelectorAll('.member-row');

            search?.addEventListener('keyup', function() {

                const value = this.value.toLowerCase();

                rows.forEach(row => {

                    const name = row.querySelector('.member-name')
                        .textContent
                        .toLowerCase();

                    row.style.display =
                        name.includes(value)
                        ? ''
                        : 'none';

                });

            });

        });
    </script>

</x-layouts.group>