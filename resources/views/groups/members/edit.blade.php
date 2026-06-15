<x-layouts.group :group="$group" :groups="$groups">
    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-800">Edit Member</h1>
                    <p class="text-sm text-slate-500 mt-1">Update member details and role for {{ $group->name }}.</p>
                </div>
                <div class="text-sm text-slate-500">
                    <a href="{{ route('groups.members.index', $group->id) }}" class="underline">Back to members</a>
                </div>
            </div>

            <form method="POST" action="{{ route('members.update', [$group->id, $member->id]) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm text-slate-600 mb-2">Name</label>
                    <input name="name" value="{{ old('name', $member->name) }}" required
                        class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Email</label>
                        <input name="email" value="{{ old('email', $member->email) }}"
                            class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-2">Phone</label>
                        <input name="phone_no" value="{{ old('phone_no', $member->phone_no) }}"
                            class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-600 mb-2">Role</label>
                    <select name="role" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none">
                        <option value="member" {{ ($member->pivot->role ?? '') === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="chairperson" {{ ($member->pivot->role ?? '') === 'chairperson' ? 'selected' : '' }}>Chairperson</option>
                        <option value="secretary" {{ ($member->pivot->role ?? '') === 'secretary' ? 'selected' : '' }}>Secretary</option>
                        <option value="treasurer" {{ ($member->pivot->role ?? '') === 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('members.show', [$group->id, $member->id]) }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.group>