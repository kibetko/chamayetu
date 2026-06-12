
<x-layouts.group
    :group="$group"
    :groups="auth()->user()->groups">

<div class="min-h-screen bg-[#D9E3F4] p-6">

    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Header --}}
            <div class="px-8 py-6 border-b bg-gradient-to-r from-blue-600 to-blue-500">

                <h2 class="text-2xl font-bold text-white">
                    Group Settings
                </h2>

                <p class="text-blue-100 mt-1">
                    Manage loan rules, contributions and leadership roles.
                </p>

            </div>
            

            <form
                method="POST"
                action="{{ route('groups.settings.update') }}"
                class="p-8">

                @csrf

                {{-- Loan Settings --}}
                <div class="mb-10">

                    <h3 class="text-lg font-semibold text-gray-800 mb-6">
                        Financial Settings
                    </h3>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Contribution Due Day
                </label>

                <input
                    type="number"
                    name="contribution_due_day"
                    min="1"
                    max="31"
                    value="{{ $group->settings?->contribution_due_day }}"
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-gray-500 mt-1">
                    Day of the month contributions must be paid.
                </p>
            </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Interest Rate (%)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="interest_rate"
                                value="{{ $group->settings?->interest_rate }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Repayment Period (Days)
                            </label>

                            <input
                                type="number"
                                name="repayment_period_days"
                                value="{{ $group->settings?->repayment_period_days }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Grace Period (Days)
                            </label>

                            <input
                                type="number"
                                name="grace_period_days"
                                value="{{ $group->settings?->grace_period_days }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Contribution
                            </label>

                            <input
                                type="number"
                                name="minimum_contribution"
                                value="{{ $group->settings?->minimum_contribution }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Loan Multiplier
                            </label>

                            <input
                                type="number"
                                name="maximum_loan_multiplier"
                                value="{{ $group->settings?->maximum_loan_multiplier }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Late Penalty Amount
                            </label>

                            <input
                                type="number"
                                name="late_penalty_amount"
                                value="{{ $group->settings?->late_penalty_amount }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Penalty Type
                            </label>

                            <select
                                name="late_penalty_type"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                <option
                                    value="fixed"
                                    @selected($group->settings?->late_penalty_type === 'fixed')>
                                    Fixed Amount
                                </option>

                                <option
                                    value="percentage"
                                    @selected($group->settings?->late_penalty_type === 'percentage')>
                                    Percentage
                                </option>

                            </select>
                        </div>

                    </div>

                </div>

                {{-- Divider --}}
                <div class="border-t my-10"></div>

                {{-- Leadership --}}
                <div>

    <h3 class="text-lg font-semibold text-gray-800 mb-6">
        Leadership Roles
    </h3>

    <div class="grid md:grid-cols-3 gap-6">

        {{-- Chairperson --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Chairperson
            </label>

            <select
                id="chairperson"
                name="chairperson_id"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                @foreach($group->members as $member)

                    <option
                        value="{{ $member->id }}"
                        @selected($chairperson?->id === $member->id)>
                        {{ $member->name }}
                    </option>

                @endforeach

            </select>
        </div>

        {{-- Secretary --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Secretary
            </label>

            <select
                id="secretary"
                name="secretary_id"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                <option value="">
                    Select Secretary
                </option>

                @foreach($group->members as $member)

                    <option
                        value="{{ $member->id }}"
                        @selected($secretary?->id === $member->id)>
                        {{ $member->name }}
                    </option>

                @endforeach

            </select>
        </div>

        {{-- Treasurer --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Treasurer
            </label>

            <select
                id="treasurer"
                name="treasurer_id"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                <option value="">
                    Select Treasurer
                </option>

                @foreach($group->members as $member)

                   <option
                        value="{{ $member->id }}"
                        @selected($treasurer?->id === $member->id)>
                        {{ $member->name }}
                    </option>

                @endforeach

            </select>
        </div>

    </div>

</div>

                {{-- Save Button --}}
                <div class="mt-10 flex justify-end">

                    <button
                        type="submit"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition">

                        Save Settings

                    </button>

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

        const selected = [
            chairperson.value,
            secretary.value,
            treasurer.value
        ].filter(Boolean);

        const unique = [...new Set(selected)];

        if (selected.length !== unique.length) {

            alert(
                'A member cannot hold multiple leadership positions.'
            );

            return false;
        }

        return true;
    }

    [chairperson, secretary, treasurer].forEach(select => {

        select.addEventListener('change', validateRoles);

    });

});
</script>
@endpush

</x-layouts.group>

