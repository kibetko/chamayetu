<x-app-layout>

<div class="min-h-screen bg-gradient-to-b from-[#F3F7F5] to-[#EAF6F0] py-10">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Available Groups</h1>
                <p class="text-sm text-slate-600 mt-1">Open a group's dashboard to view contributions, loans and members.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-full shadow hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Group
                </a>
            </div>
        </div>

        <!-- Groups grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups as $group)
                <a href="{{ route('groups.switch', $group->id) }}" class="group block bg-white rounded-2xl shadow hover:shadow-lg transform hover:-translate-y-1 transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">{{ $group->name }}</h2>
                                <p class="text-sm text-slate-500 mt-1">{{ Str::limit($group->description, 100) }}</p>
                            </div>

                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $group->active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $group->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-emerald-50 p-3 rounded-lg">
                                    <svg class="w-5 h-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"/></svg>
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500">Members</div>
                                    <div class="font-medium text-slate-800">{{ $group->members->count() ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs text-slate-400">Code</div>
                                <div class="font-medium text-slate-700">{{ $group->unique_code }}</div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-xs text-slate-400">
                                Created {{ optional($group->created_at)->diffForHumans() }}
                            </div>

                            <div class="hidden sm:flex items-center gap-2">
                                <span class="text-xs text-slate-500">Open dashboard</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</div>

</x-app-layout>