<x-layouts.group :group="$group" :groups="$groups">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-800">Member — {{ $member->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Contact and role details</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('members.edit', [$group->id, $member->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg shadow-sm hover:bg-emerald-700">Edit</a>
                    <a href="{{ route('groups.members.index', $group->id) }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Back</a>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center justify-center">
                    <div class="h-28 w-28 rounded-full bg-emerald-100 flex items-center justify-center text-3xl text-emerald-700 font-bold">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                </div>

                <div class="md:col-span-2 space-y-4">
                    <div>
                        <div class="text-xs text-slate-500">Email</div>
                        <div class="text-sm font-medium text-slate-800">{{ $member->email ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-slate-500">Phone</div>
                        <div class="text-sm font-medium text-slate-800">{{ $member->phone_no ?? '-' }}</div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-500">Role</div>
                            <div class="text-sm font-medium text-slate-800">{{ ucfirst($member->pivot->role ?? 'member') }}</div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-slate-500">Joined</div>
                            <div class="text-sm text-slate-600">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d M Y') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.group>