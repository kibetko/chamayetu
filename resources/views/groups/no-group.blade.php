<x-app-layout>

<div class="min-h-screen bg-[#D9E3F4] flex items-center justify-center p-6">


<div class="max-w-2xl w-full">

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-8 py-10 text-center">

            

            <h1 class="text-3xl font-bold text-white">
                Welcome to ChamaYetu
            </h1>

            <p class="text-blue-100 mt-3">
                Create a new savings group or join an existing one to start contributing, borrowing and growing together.
            </p>

        </div>

        {{-- Body --}}
        <div class="p-8">

            <div class="grid md:grid-cols-3 gap-4 mb-8">

                <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">

                    <h3 class="font-semibold text-blue-800 mb-2">
                        Save Together
                    </h3>

                    <p class="text-sm text-slate-600">
                        Track contributions and build your group fund.
                    </p>

                </div>

                <div class="bg-green-50 rounded-2xl p-5 border border-green-100">

                    <h3 class="font-semibold text-green-800 mb-2">
                        Manage Loans
                    </h3>

                    <p class="text-sm text-slate-600">
                        Apply for and monitor loans within your group.
                    </p>

                </div>

                <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100">

                    <h3 class="font-semibold text-purple-800 mb-2">
                        Stay Organized
                    </h3>

                    <p class="text-sm text-slate-600">
                        Keep records of payments, members and finances.
                    </p>

                </div>

            </div>

            <div class="text-center mb-8">

                <h2 class="text-xl font-bold text-slate-800">
                    You're not part of any group yet
                </h2>

                <p class="text-slate-500 mt-2">
                    Create your own group or join one using a group code.
                </p>

            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4">

                <a
                    href="{{ route('groups.create') }}"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-4 rounded-2xl font-semibold shadow-md transition">

                    Create Group

                </a>

                <a
                    href="{{ route('groups.join') }}"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-6 py-4 rounded-2xl font-semibold shadow-md transition">

                    Join Group

                </a>

            </div>

        </div>

    </div>

</div>


</div>

</x-app-layout>
