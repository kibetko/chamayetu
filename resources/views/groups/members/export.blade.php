<x-layouts.group :group="$group" :groups="$groups">
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-800">Export Members</h1>
                    <p class="text-sm text-slate-500 mt-1">Download the member list for <strong>{{ $group->name }}</strong>.</p>
                </div>

                <a href="{{ route('groups.members.index', $group->id) }}" class="text-sm text-slate-500 underline">Back</a>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('groups.members.export', $group->id) }}?format=csv" class="block rounded-lg border border-slate-200 p-4 hover:shadow transition">
                        <div class="text-sm text-slate-500">CSV</div>
                        <div class="mt-2 font-medium text-slate-800">Download CSV</div>
                        <div class="text-xs text-slate-400 mt-1">Comma-separated values file for spreadsheets.</div>
                    </a>

                    <a href="{{ route('groups.members.export', $group->id) }}?format=xlsx" class="block rounded-lg border border-slate-200 p-4 hover:shadow transition">
                        <div class="text-sm text-slate-500">XLSX</div>
                        <div class="mt-2 font-medium text-slate-800">Download XLSX</div>
                        <div class="text-xs text-slate-400 mt-1">Excel file (if supported by controller).</div>
                    </a>
                </div>

                <div class="text-sm text-slate-500">If XLSX is not implemented the CSV option will work for most spreadsheet apps.</div>
            </div>
        </div>
    </div>
</x-layouts.group>