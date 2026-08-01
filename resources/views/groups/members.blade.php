<x-layouts.group :group="$group" :groups="$groups">

<div class="rounded-2xl bg-[#D9E3F4] p-4 lg:p-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Group Members
            </h1>

            <p class="text-slate-600 mt-1">
                Manage members of
                <span class="font-semibold text-emerald-600">
                    {{ $group->name }}
                </span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">

            <a
                href="{{ route('members.invite',$group->id) }}"
                class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">

                Invite Member

            </a>

            <a
                href="{{ route('groups.members.export',$group->id) }}"
                class="px-4 py-2 rounded-xl border bg-white hover:bg-slate-50">

                Export CSV

            </a>

        </div>

    </div>



    {{-- SUMMARY CARDS --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-sm p-5">

            <div class="text-sm text-slate-500">
                Total Members
            </div>

            <div class="text-3xl font-bold mt-2 text-slate-800">

                {{ $members->count() }}

            </div>

        </div>



        <div class="bg-white rounded-xl shadow-sm p-5">

            <div class="text-sm text-slate-500">

                Officials

            </div>

            <div class="text-3xl font-bold mt-2 text-emerald-600">

                {{ $members->whereIn('pivot.role',['chairperson','secretary','treasurer'])->count() }}

            </div>

        </div>



        <div class="bg-white rounded-xl shadow-sm p-5">

            <div class="text-sm text-slate-500">

                Online Members

            </div>

            <div class="text-3xl font-bold mt-2 text-green-600">

                {{ isset($onlineMembers) ? $onlineMembers->count() : 0 }}

            </div>

        </div>

    </div>




    {{-- SEARCH + FILTERS --}}

    <div class="flex flex-col lg:flex-row gap-3 mb-6">

        <div class="relative flex-1">

            <input

                id="memberSearch"

                type="text"

                placeholder="Search member..."

                class="w-full rounded-xl border px-4 py-3 pl-11 focus:ring-2 focus:ring-emerald-400">

            <svg
                class="absolute left-3 top-3.5 h-5 w-5 text-slate-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-5-5m2-5a7 7 0 11-14 0a7 7 0 0114 0z"/>

            </svg>

        </div>

        <select
            id="roleFilter"
            class="rounded-xl border px-4 py-3 bg-white">

            <option value="all">All Roles</option>

            <option value="chairperson">Chairperson</option>

            <option value="secretary">Secretary</option>

            <option value="treasurer">Treasurer</option>

            <option value="member">Member</option>

        </select>

    </div>



    @php

        $members = $group->members
            ->sortBy(function($member){

                return match($member->pivot->role){

                    'chairperson'=>1,

                    'secretary'=>2,

                    'treasurer'=>3,

                    default=>4

                };

            })
            ->values();

    @endphp




    {{-- MOBILE CARDS --}}

    <div
        id="membersCards"
        class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden">

        @foreach($members as $member)

        <div

            class="member-card bg-white rounded-xl shadow-sm p-4"

            data-name="{{ strtolower($member->name) }}"

            data-email="{{ strtolower($member->email ?? '') }}"

            data-phone="{{ strtolower($member->phone_no ?? '') }}"

            data-role="{{ strtolower($member->pivot->role ?? 'member') }}">

            <div class="flex justify-between">

                <div class="flex items-center gap-3">

                    <div class="relative">

                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700">

                            {{ strtoupper(substr($member->name,0,1)) }}

                        </div>

                        @if(isset($onlineMembers) && $onlineMembers->contains('id',$member->id))

                            <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 border-2 border-white rounded-full"></span>

                        @endif

                    </div>

                    <div>

                        <div class="font-semibold">

                            {{ $member->name }}

                        </div>

                        <div class="text-xs text-slate-500">

                            {{ $member->email }}

                        </div>

                    </div>

                </div>

                <span class="text-xs rounded-full bg-emerald-100 text-emerald-700 px-3 py-1">

                    {{ ucfirst($member->pivot->role) }}

                </span>

            </div>

            <div class="mt-4 text-sm text-slate-600">

                {{ $member->phone_no }}

            </div>

            <div class="mt-5 flex justify-between items-center">

                <small class="text-slate-400">

                    Joined

                    {{ $member->pivot->joined_at
                        ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y')
                        : '-' }}

                </small>

                <div class="space-x-3">

                    <a
                        href="{{ route('members.show',[$group->id,$member->id]) }}"
                        class="text-emerald-600 font-medium">

                        View

                    </a>

                    @if(method_exists($group,'isChairperson') && $group->isChairperson())

                        <a
                            href="{{ route('members.edit',[$group->id,$member->id]) }}"
                            class="text-slate-700">

                            Edit

                        </a>

                    @endif

                </div>

            </div>

        </div>

        @endforeach

    </div>

    {{-- DESKTOP TABLE STARTS BELOW --}}
    <div class="hidden md:block overflow-x-auto mt-6">
                <table class="min-w-full bg-white rounded-xl shadow-sm overflow-hidden">

            <thead class="bg-slate-100">

                <tr>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                        Member
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                        Email
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                        Phone
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                        Role
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                        Joined
                    </th>

                    <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

            @foreach($members as $member)

                <tr

                    class="member-row border-t hover:bg-slate-50 transition"

                    data-name="{{ strtolower($member->name) }}"
                    data-email="{{ strtolower($member->email ?? '') }}"
                    data-phone="{{ strtolower($member->phone_no ?? '') }}"
                    data-role="{{ strtolower($member->pivot->role ?? 'member') }}">

                    <td class="px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div class="relative">

                                <div class="h-11 w-11 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700">

                                    {{ strtoupper(substr($member->name,0,1)) }}

                                </div>

                                @if(isset($onlineMembers) && $onlineMembers->contains('id',$member->id))

                                    <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-white"></span>

                                @endif

                            </div>

                            <div>

                                <div class="font-semibold text-slate-800">

                                    {{ $member->name }}

                                </div>

                                <div class="text-xs text-slate-500">

                                    ID #{{ $member->id }}

                                </div>

                            </div>

                        </div>

                    </td>

                    <td class="px-6 py-4 text-slate-600">

                        {{ $member->email ?? '-' }}

                    </td>

                    <td class="px-6 py-4 text-slate-600">

                        {{ $member->phone_no ?? '-' }}

                    </td>

                    <td class="px-6 py-4">

                        @php

                            $badge = match($member->pivot->role){

                                'chairperson'=>'bg-emerald-100 text-emerald-700',

                                'secretary'=>'bg-blue-100 text-blue-700',

                                'treasurer'=>'bg-yellow-100 text-yellow-700',

                                default=>'bg-slate-100 text-slate-700'

                            };

                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">

                            {{ ucfirst($member->pivot->role) }}

                        </span>

                    </td>

                    <td class="px-6 py-4 text-slate-600">

                        {{ $member->pivot->joined_at
                            ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y')
                            : '-' }}

                    </td>

                    <td class="px-6 py-4 text-right">

                        <a

                            href="{{ route('members.show',[$group->id,$member->id]) }}"

                            class="text-emerald-600 hover:underline font-medium">

                            View

                        </a>

                        @if(method_exists($group,'isChairperson') && $group->isChairperson())

                            <a

                                href="{{ route('members.edit',[$group->id,$member->id]) }}"

                                class="ml-4 text-slate-700 hover:underline">

                                Edit

                            </a>

                        @endif

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

    const search = document.getElementById('memberSearch');

    const role = document.getElementById('roleFilter');

    const rows = document.querySelectorAll('.member-row');

    const cards = document.querySelectorAll('.member-card');

    function filter(){

        const q = search.value.toLowerCase().trim();

        const r = role.value;

        function matches(el){

            const name = el.dataset.name || '';

            const email = el.dataset.email || '';

            const phone = el.dataset.phone || '';

            const userRole = el.dataset.role || '';

            const textMatch =

                name.includes(q) ||

                email.includes(q) ||

                phone.includes(q);

            const roleMatch =

                r === 'all' ||

                userRole === r;

            return textMatch && roleMatch;

        }

        rows.forEach(row=>{

            row.style.display = matches(row) ? '' : 'none';

        });

        cards.forEach(card=>{

            card.style.display = matches(card) ? '' : 'none';

        });

    }

    search.addEventListener('keyup',filter);

    role.addEventListener('change',filter);

});

</script>

@endpush

</x-layouts.group>