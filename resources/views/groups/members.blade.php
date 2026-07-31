<x-layouts.group
    :group="$group"
    :groups="$groups">

    <div class="rounded-2xl bg-[#D9E3F4] p-3 sm:p-4 lg:p-6">

        <!-- Header -->
<div class="mb-6 flex flex-col gap-3 sm:gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                    Group Members
                </h1>

                <p class="mt-1 text-sm text-slate-600">
                    Manage and view all registered members for <span class="font-semibold text-emerald-600">{{ $group->name }}</span>.
                </p>
            </div>

            <!-- Controls -->
            <div class="flex items-center justify-between gap-3">

                <div class="relative w-full sm:flex-1 md:w-80">
                    <label for="memberSearch" class="sr-only">Search members</label>
                    <input
                        type="text"
                        id="memberSearch"
                        placeholder="Search name, email or phone..."
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-10 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <svg class="absolute left-3 top-3.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                    </svg>
                </div>

                <select id="roleFilter" class="hidden md:block rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm focus:outline-none">
                    <option value="all">All roles</option>
                    <option value="chairperson">Chairperson</option>
                    <option value="secretary">Secretary</option>
                    <option value="treasurer">Treasurer</option>
                    <option value="member">Member</option>
                </select>

                <div class="flex items-center gap-2">
                    <a href="{{ route('members.invite', $group->id) }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-2xl shadow hover:bg-emerald-700 transition text-sm">
                        Invite
                    </a>

                    <a href="{{ route('groups.members.export', $group->id) }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-3 py-2 rounded-2xl shadow-sm hover:shadow transition text-sm">
                        Export
                    </a>
                </div>

            </div>

        </div>

        @php
            $members = $group->members->sortBy(function ($member) {
                return match ($member->pivot->role) {
                    'chairperson' => 1,
                    'secretary' => 2,
                    'treasurer' => 3,
                    default => 4,
                };
            })->values();
        @endphp

        <!-- Cards (mobile) -->
<div id="membersCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:hidden mb-6">

@foreach($members as $member)

<div class="member-card bg-white rounded-xl p-4 shadow-sm"
    data-name="{{ strtolower($member->name) }}"
    data-role="{{ strtolower($member->pivot->role ?? 'member') }}"
    data-email="{{ strtolower($member->email ?? '') }}"
    data-phone="{{ strtolower($member->phone_no ?? '') }}">


    {{-- TOP ROW --}}
    <div class="flex items-center justify-between gap-3">


        {{-- LEFT: Avatar + Name --}}
        <div class="flex items-center gap-3 min-w-0">


            <div class="h-10 w-10 shrink-0 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold">

                {{ strtoupper(substr($member->name, 0, 1)) }}

            </div>


            <div class="min-w-0">

                <div class="font-medium text-slate-800 truncate">

                    {{ $member->name }}

                </div>


                <div class="text-xs text-slate-400 truncate">

                    {{ $member->email ?? '-' }}

                </div>


            </div>


        </div>



        {{-- RIGHT: Role + Date --}}
        <div class="text-right shrink-0">


            <div class="text-xs text-slate-500">

                {{ $member->pivot->joined_at 
                    ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y') 
                    : '-' 
                }}

            </div>


            <div class="mt-2">

                <span class="rounded-full px-3 py-1 text-xs font-semibold
                    {{ $member->pivot->role === 'chairperson'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-100 text-slate-700' }}">

                    {{ ucfirst($member->pivot->role ?? 'member') }}

                </span>

            </div>


        </div>


    </div>



    {{-- BOTTOM ROW --}}
    <div class="mt-4 flex items-center justify-between text-sm text-slate-600">


        <div class="truncate">

            {{ $member->phone_no ?? '-' }}

        </div>



        <div class="flex items-center gap-3">

            <a href="{{ route('members.show', [$group->id, $member->id]) }}"
               class="text-emerald-600 text-sm font-medium">

                View

            </a>


            @if(method_exists($group, 'isChairperson') ? $group->isChairperson() : false)

            <a href="{{ route('members.edit', [$group->id, $member->id]) }}"
               class="text-slate-600 text-sm font-medium">

                Edit

            </a>

            @endif


        </div>


    </div>


</div>

@endforeach

</div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('memberSearch');
            const roleFilter = document.getElementById('roleFilter');
            const rows = Array.from(document.querySelectorAll('.member-row'));
            const cards = Array.from(document.querySelectorAll('.member-card'));

            function matches(el, q, role) {
                const name = (el.getAttribute('data-name') || '');
                const email = (el.getAttribute('data-email') || '');
                const phone = (el.getAttribute('data-phone') || '');
                const r = (el.getAttribute('data-role') || 'member');

                const textMatch = !q || name.includes(q) || email.includes(q) || phone.includes(q);
                const roleMatch = !role || role === 'all' || r === role;
                return textMatch && roleMatch;
            }

            function applyFilters() {
                const q = (search.value || '').trim().toLowerCase();
                const role = roleFilter ? roleFilter.value : 'all';

                rows.forEach(row => row.style.display = matches(row, q, role) ? '' : 'none');
                cards.forEach(card => card.style.display = matches(card, q, role) ? '' : 'none');
            }

            search?.addEventListener('input', applyFilters);
            roleFilter?.addEventListener('change', applyFilters);
        });
    </script>
    @endpush

</x-layouts.group>