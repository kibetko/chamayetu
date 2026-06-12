<x-layouts.group
    :group="$group"
    :groups="$groups">

    <div class="bg-[#D9E3F4] min-h-screen p-6">
            <div class="min-h-screen bg-[#D9E3F4] p-6">

    <div class="max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="mb-8">

            <h1 class="text-3xl font-bold text-slate-800">
                Help Center
            </h1>

            <p class="text-slate-600 mt-2">
                Contact support, view group information and stay updated with group announcements.
            </p>

        </div>

        <!-- Top Section -->
        <div class="grid lg:grid-cols-2 gap-6">

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-lg p-6">

                <h2 class="text-xl font-bold text-slate-800 mb-5">
                    Contact Support
                </h2>

                <form
                    method="POST"
                    action="{{ route('help-center.contact') }}"
                    class="space-y-4">

                    @csrf

                    <div>

                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Name
                        </label>

                        <input
                            type="text"
                            value="{{ auth()->user()->name }}"
                            readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            value="{{ auth()->user()->phone_no }}"
                            readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Subject
                        </label>

                        <input
                            type="text"
                            name="subject"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-slate-600 mb-1">
                            Issue
                        </label>

                        <textarea
                            name="issue"
                            rows="5"
                            required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-blue-500"></textarea>

                    </div>

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-6 py-3 text-white font-semibold hover:bg-blue-700">

                        Submit Request

                    </button>

                </form>

            </div>

            <!-- Group Information -->
            <div class="space-y-6">

                <div class="bg-white rounded-2xl shadow-lg p-6">

                    <h2 class="text-xl font-bold text-slate-800 mb-4">
                        Group Information
                    </h2>

                    <div class="space-y-3">

                        <div>

                            <p class="text-sm text-slate-500">
                                Group Name
                            </p>

                            <p class="font-semibold text-slate-800">
                                {{ $group->name }}
                            </p>

                        </div>

                        <div>

                            <p class="text-sm text-slate-500">
                                Description
                            </p>

                            <p class="text-slate-700">
                                {{ $group->description }}
                            </p>

                        </div>

                        <div>

    <p class="text-sm text-slate-500">
        Created On
    </p>

    <p class="font-medium text-slate-700">
        {{ $group->created_at->format('d M Y') }}
    </p>

</div>

<hr class="my-4">

<div>

    <h3 class="font-bold text-slate-800 mb-3">
        Group Loan Settings
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Interest Rate
            </p>
            <p class="font-semibold text-slate-800">
                {{ $group->settings?->interest_rate ?? 0 }}%
            </p>
        </div>

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Repayment Period
            </p>
            <p class="font-semibold text-slate-800">
                {{ $group->settings?->repayment_period_days ?? 0 }} Days
            </p>
        </div>

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Grace Period
            </p>
            <p class="font-semibold text-slate-800">
                {{ $group->settings?->grace_period_days ?? 0 }} Days
            </p>
        </div>

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Minimum Contribution
            </p>
            <p class="font-semibold text-emerald-600">
                KES {{ number_format($group->settings?->minimum_contribution ?? 0) }}
            </p>
        </div>

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Maximum Loan Multiplier
            </p>
            <p class="font-semibold text-blue-600">
                {{ $group->settings?->maximum_loan_multiplier ?? 0 }}x
            </p>
        </div>

        <div class="rounded-xl bg-slate-50 p-3">
            <p class="text-xs text-slate-500">
                Late Penalty
            </p>
            <p class="font-semibold text-red-600">
                {{ $group->settings?->late_penalty_amount ?? 0 }}
                {{ $group->settings?->late_penalty_type === 'percentage' ? '%' : 'KES' }}
            </p>
        </div>

    </div>

</div>

                    </div>

                </div>

                <!-- Leadership Team -->
                <div class="bg-white rounded-2xl shadow-lg p-6">

                    <h2 class="text-xl font-bold text-slate-800 mb-4">
                        Leadership Team
                    </h2>

                    <div class="space-y-4">

                        <div class="flex items-center justify-between">

                            <span class="text-slate-600">
                                Chairperson
                            </span>

                            <span class="font-semibold text-blue-600">
                                {{ $chairperson?->name ?? 'Not Assigned' }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between">

                            <span class="text-slate-600">
                                Secretary
                            </span>

                            <span class="font-semibold text-green-600">
                                {{ $secretary?->name ?? 'Not Assigned' }}
                            </span>

                        </div>

                        <div class="flex items-center justify-between">

                            <span class="text-slate-600">
                                Treasurer
                            </span>

                            <span class="font-semibold text-purple-600">
                                {{ $treasurer?->name ?? 'Not Assigned' }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Leader Update Form -->
        @if($group->isLeader())

        <div class="bg-white rounded-2xl shadow-lg p-6 mt-8">

            <h2 class="text-xl font-bold text-slate-800 mb-5">
                Publish Group Update
            </h2>

            <form
                method="POST"
                action="{{ route('group-updates.store') }}"
                enctype="multipart/form-data"
                class="space-y-4">

                @csrf

                <input
                    type="text"
                    name="title"
                    placeholder="Update Title"
                    required
                    class="w-full rounded-xl border border-slate-200 px-4 py-3">

                <textarea
                    name="content"
                    rows="5"
                    placeholder="Write update details..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3"></textarea>

                <input
                    type="file"
                    name="attachment"
                    class="w-full rounded-xl border border-slate-200 p-3">

                <button
                    type="submit"
                    class="rounded-xl bg-emerald-600 px-6 py-3 text-white font-semibold hover:bg-emerald-700">

                    Publish Update

                </button>

            </form>

        </div>

        @endif

        <!-- Updates Feed -->
        <div class="mt-8">

            <h2 class="text-2xl font-bold text-slate-800 mb-5">
                Recent Group Updates
            </h2>

            <div class="space-y-4">

                @forelse($updates as $update)

                    <div class="bg-white rounded-2xl shadow-lg p-6">

                        <div class="flex justify-between items-start">

                            <div>

                                <h3 class="text-lg font-bold text-slate-800">
                                    {{ $update->title }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    {{ $update->created_at->diffForHumans() }}
                                </p>

                            </div>

                        </div>

                        <p class="mt-4 text-slate-700">
                            {{ $update->content }}
                        </p>

                        @if($update->attachment)

                            <a
                                href="{{ asset('storage/'.$update->attachment) }}"
                                target="_blank"
                                class="inline-block mt-4 rounded-lg bg-red-100 px-4 py-2 text-red-700 font-medium hover:bg-red-200">

                                📄 Download Attachment

                            </a>

                        @endif

                    </div>

                @empty

                    <div class="bg-white rounded-2xl shadow-lg p-10 text-center text-slate-500">

                        No updates have been published yet.

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>
     

    </div>

</x-layouts.group>