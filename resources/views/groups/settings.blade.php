
<x-layouts.group :group="$group" :groups="auth()->user()->groups">

<div class="min-h-screen bg-[#F3F7F5] p-6">
    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            <div class="px-6 py-5 bg-gradient-to-r from-emerald-600 to-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Group Settings</h2>
                        <p class="text-sm text-emerald-100 mt-1">Manage loan rules, contributions and leadership roles.</p>
                    </div>

                    <div class="text-sm text-emerald-50">
                        <span class="font-semibold">{{ $group->name }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('groups.settings.update') }}" class="p-6">
                @csrf

                <div class="grid lg:grid-cols-2 gap-6">

                    <div class="space-y-6">
                        <div class="bg-white border rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-semibold text-slate-800 mb-3">Financial Settings</h3>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Contribution Due Day</label>
                                    <input type="number" name="contribution_due_day" min="1" max="31" value="{{ $group->settings?->contribution_due_day }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                    <p class="text-xs text-slate-400 mt-1">Day of the month contributions must be paid.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Interest Rate (%)</label>
                                    <input type="number" step="0.01" name="interest_rate" value="{{ $group->settings?->interest_rate }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Repayment Period (Days)</label>
                                    <input type="number" name="repayment_period_days" value="{{ $group->settings?->repayment_period_days }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Grace Period (Days)</label>
                                    <input type="number" name="grace_period_days" value="{{ $group->settings?->grace_period_days }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Minimum Contribution</label>
                                    <input type="number" name="minimum_contribution" value="{{ $group->settings?->minimum_contribution }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Maximum Loan Multiplier</label>
                                    <input type="number" name="maximum_loan_multiplier" value="{{ $group->settings?->maximum_loan_multiplier }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Late Penalty Amount</label>
                                    <input type="number" name="late_penalty_amount" value="{{ $group->settings?->late_penalty_amount }}" class="w-full rounded-md border border-slate-200 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Penalty Type</label>
                                    <select name="late_penalty_type" class="w-full rounded-md border border-slate-200 px-3 py-2">
                                        <option value="fixed" @selected($group->settings?->late_penalty_type === 'fixed')>Fixed Amount</option>
                                        <option value="percentage" @selected($group->settings?->late_penalty_type === 'percentage')>Percentage</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-md font-semibold hover:bg-emerald-700">Save Settings</button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white border rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-semibold text-slate-800 mb-3">Leadership Roles</h3>

                            <div class="grid gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Chairperson</label>
                                    <select id="chairperson" name="chairperson_id" class="w-full rounded-md border border-slate-200 px-3 py-2">
                                        @foreach($group->members as $member)
                                            <option value="{{ $member->id }}" @selected($chairperson?->id === $member->id)>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Secretary</label>
                                    <select id="secretary" name="secretary_id" class="w-full rounded-md border border-slate-200 px-3 py-2">
                                        <option value="">Select Secretary</option>
                                        @foreach($group->members as $member)
                                            <option value="{{ $member->id }}" @selected($secretary?->id === $member->id)>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Treasurer</label>
                                    <select id="treasurer" name="treasurer_id" class="w-full rounded-md border border-slate-200 px-3 py-2">
                                        <option value="">Select Treasurer</option>
                                        @foreach($group->members as $member)
                                            <option value="{{ $member->id }}" @selected($treasurer?->id === $member->id)>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border rounded-lg p-4 shadow-sm">
                            <h4 class="text-sm text-slate-600">Notes</h4>
                            <p class="text-xs text-slate-500 mt-2">Changes to leadership roles are immediately effective. Use caution when assigning multiple roles.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chairperson = document.getElementById('chairperson');
    const secretary = document.getElementById('secretary');
    const treasurer = document.getElementById('treasurer');

    function validateRoles() {
        const selected = [chairperson.value, secretary.value, treasurer.value].filter(Boolean);
        const unique = [...new Set(selected)];
        if (selected.length !== unique.length) {
            alert('A member cannot hold multiple leadership positions.');
            return false;
        }
        return true;
    }

    [chairperson, secretary, treasurer].forEach(select => select?.addEventListener('change', validateRoles));
});
</script>
@endpush

</x-layouts.group>

