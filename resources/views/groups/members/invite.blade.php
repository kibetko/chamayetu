<x-layouts.group :group="$group" :groups="$groups">
    <div class="max-w-2xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white">
                <h1 class="text-2xl font-semibold">Invite Member</h1>
                <p class="text-sm mt-1 opacity-90">Send an invitation to join <strong>{{ $group->name }}</strong>.</p>
            </div>

            <form method="POST" action="{{ route('members.invite.store', $group->id) }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-slate-600 mb-2">Email</label>
                    <input name="email" type="email" required placeholder="member@example.com"
                        class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500" />
                </div>

                <div>
                    <label class="block text-sm text-slate-600 mb-2">Message (optional)</label>
                    <textarea name="message" rows="4" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('groups.members.index', $group->id) }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">Send Invite</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.group>