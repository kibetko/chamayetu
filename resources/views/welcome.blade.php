
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChamaYetu - Smart Chama Management</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563eb',
                        secondary: '#0f172a',
                        accent: '#14b8a6',
                    }
                }
            }
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .glass {
            backdrop-filter: blur(12px);
            background: rgba(255,255,255,0.75);
        }

        .hero-bg {
            background:
                radial-gradient(circle at top left,#60a5fa 0%,transparent 40%),
                radial-gradient(circle at bottom right,#14b8a6 0%,transparent 40%);
        }
        @keyframes flow {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.animate-flow {
    animation: flow 6s linear infinite;
    width: 200%;
    opacity: 0.8;
}

@keyframes flowNodes {
    0% {
        transform: translateX(-20%);
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateX(20%);
        opacity: 0.6;
    }
}

.animate-flow-nodes {
    animation: flowNodes 6s ease-in-out infinite;
}
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans">

<!-- NAVBAR -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">

    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <div class="flex items-center justify-between h-20">

            <div class="flex items-center gap-2">

                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-600 to-teal-500"></div>

                <span class="font-extrabold text-2xl">
                    Chama<span class="text-blue-600">Yetu</span>
                </span>

            </div>

            <div class="hidden lg:flex items-center gap-8">

                <a href="#features" class="hover:text-blue-600 transition">
                    Features
                </a>

                <a href="#how-it-works" class="hover:text-blue-600 transition">
                    How It Works
                </a>

                <a href="#testimonials" class="hover:text-blue-600 transition">
                    Testimonials
                </a>

                <a href="#faq" class="hover:text-blue-600 transition">
                    FAQ
                </a>

            </div>

            <div class="flex gap-3">

                <a href="/login"
                   class="px-5 py-2 border border-slate-300 rounded-xl hover:bg-slate-100 transition">
                    Login
                </a>

                <a href="/register"
                   class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                    Get Started
                </a>

            </div>

        </div>

    </div>

</nav>

<!-- HERO -->
<section class="pt-36 pb-24 hero-bg">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>

                <span class="inline-flex px-4 py-2 rounded-full bg-blue-100 text-blue-700 font-medium">
                    Smart Chama Management Platform
                </span>

                <h1 class="text-5xl lg:text-7xl font-black leading-tight mt-6">

                    Manage Your
                    <span class="text-blue-600">
                        Chama
                    </span>

                    The Modern Way

                </h1>

                <p class="mt-8 text-xl text-slate-600 leading-relaxed">

                    Track contributions, manage loans, approve members,
                    monitor repayments, and grow your savings group —
                    all from one powerful platform.

                </p>

                <div class="flex flex-wrap gap-4 mt-10">

                    <a href="/register"
                       class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-semibold hover:bg-blue-700 transition shadow-lg">

                        Create Free Account

                    </a>

                    <a href="#features"
                       class="px-8 py-4 bg-white rounded-2xl border border-slate-300 font-semibold hover:bg-slate-100 transition">

                        Explore Features

                    </a>

                </div>

            </div>

            <div>

                <div class="bg-white rounded-3xl shadow-2xl p-8">

                    <div class="grid grid-cols-2 gap-4">

                        <div class="bg-blue-50 p-6 rounded-2xl">
                            <p class="text-sm text-slate-500">Members</p>
                            <h3 class="text-3xl font-bold mt-2">250+</h3>
                        </div>

                        <div class="bg-teal-50 p-6 rounded-2xl">
                            <p class="text-sm text-slate-500">Loans Issued</p>
                            <h3 class="text-3xl font-bold mt-2">KES 5M+</h3>
                        </div>

                        <div class="bg-purple-50 p-6 rounded-2xl">
                            <p class="text-sm text-slate-500">Contributions</p>
                            <h3 class="text-3xl font-bold mt-2">KES 20M+</h3>
                        </div>

                        <div class="bg-green-50 p-6 rounded-2xl">
                            <p class="text-sm text-slate-500">Groups</p>
                            <h3 class="text-3xl font-bold mt-2">100+</h3>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="py-10 bg-white border-y border-slate-200">

    <div class="max-w-7xl mx-auto px-6">

        <p class="text-center text-sm uppercase tracking-widest text-slate-500 mb-8">
            Trusted by savings groups across Kenya
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

            <div class="bg-slate-50 rounded-2xl p-6 text-center">
                🔒
                <p class="font-semibold mt-2">Secure Data</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 text-center">
                📱
                <p class="font-semibold mt-2">Mobile Friendly</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 text-center">
                ⚡
                <p class="font-semibold mt-2">Real-Time Updates</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 text-center">
                ☁️
                <p class="font-semibold mt-2">Cloud Hosted</p>
            </div>

        </div>

    </div>

</section>

<section class="py-24 bg-slate-950 text-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid md:grid-cols-4 gap-8 text-center">

            <div>
                <h3 class="counter text-5xl font-black" data-target="250">
                    0
                </h3>
                <p class="mt-3 text-slate-400">
                    Active Groups
                </p>
            </div>

            <div>
                <h3 class="counter text-5xl font-black" data-target="5000">
                    0
                </h3>
                <p class="mt-3 text-slate-400">
                    Members
                </p>
            </div>

            <div>
                <h3 class="counter text-5xl font-black" data-target="20">
                    0
                </h3>
                <p class="mt-3 text-slate-400">
                    Million Saved
                </p>
            </div>

            <div>
                <h3 class="counter text-5xl font-black" data-target="8">
                    0
                </h3>
                <p class="mt-3 text-slate-400">
                    Counties Served
                </p>
            </div>

        </div>

    </div>

</section>

<!-- FEATURES -->
<section id="features" class="py-24">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">

            <h2 class="text-5xl font-bold">
                Everything Your Chama Needs
            </h2>

            <p class="text-slate-600 mt-4 text-lg">
                Built specifically for savings groups and community lending.
            </p>

        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            @php
                $features = [
                    ['💰','Contribution Tracking'],
                    ['🏦','Loan Management'],
                    ['👥','Member Management'],
                    ['📊','Financial Reports'],
                    ['🔔','Penalty Notifications'],
                    ['📱','Mobile Friendly']
                ];
            @endphp

            @foreach($features as $feature)

            <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-xl transition">

                <div class="text-5xl mb-5">
                    {{ $feature[0] }}
                </div>

                <h3 class="text-xl font-bold mb-3">
                    {{ $feature[1] }}
                </h3>

                <p class="text-slate-600">
                    Manage your chama efficiently with modern tools and real-time updates.
                </p>

            </div>

            @endforeach

        </div>

    </div>

</section>

<!-- HOW IT WORKS -->
<section id="how-it-works" class="py-24 bg-gradient-to-b from-white to-slate-50">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="inline-flex px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                Simple & Transparent
            </span>

            <h2 class="text-5xl font-bold text-slate-900 mt-6">
                How ChamaYetu Works
            </h2>

            <p class="mt-5 text-lg text-slate-600 max-w-3xl mx-auto">
                From creating your chama to managing loans and repayments,
                everything is designed to be simple, secure and efficient.
            </p>

        </div>

        <div class="relative">

            <!-- Connection line -->
            <div class="hidden lg:block absolute top-12 left-0 right-0 h-1 rounded-full overflow-hidden">

    <!-- Base subtle track -->
    <div class="absolute inset-0 bg-slate-200/40"></div>

    <!-- Soft gradient glow -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 via-cyan-400/20 to-emerald-500/20"></div>

    <!-- Moving pulse container -->
    <div class="absolute inset-0 animate-flow-nodes flex items-center">

        <!-- Node 1 -->
        <span class="w-3 h-3 bg-blue-500 rounded-full shadow-lg shadow-blue-500/50"></span>

        <span class="flex-1"></span>

        <!-- Node 2 -->
        <span class="w-3 h-3 bg-cyan-400 rounded-full shadow-lg shadow-cyan-400/50"></span>

        <span class="flex-1"></span>

        <!-- Node 3 -->
        <span class="w-3 h-3 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/50"></span>

        <span class="flex-1"></span>

        <!-- Node 4 -->
        <span class="w-3 h-3 bg-blue-600 rounded-full shadow-lg shadow-blue-600/50"></span>

    </div>

</div>
            <div class="grid lg:grid-cols-4 gap-8 relative">

                <!-- Step 1 -->
                <div class="group">

                    <div class="w-24 h-24 mx-auto rounded-3xl bg-blue-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition">
                        👥
                    </div>

                    <div class="mt-8 bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition">

                        <span class="text-blue-600 font-bold text-sm">
                            STEP 01
                        </span>

                        <h3 class="text-2xl font-bold mt-3 mb-4">
                            Create a Group
                        </h3>

                        <p class="text-slate-600 leading-relaxed">
                            Set up your savings group, define contribution rules,
                            loan policies, penalties and member roles.
                        </p>

                    </div>

                </div>

                <!-- Step 2 -->
                <div class="group">

                    <div class="w-24 h-24 mx-auto rounded-3xl bg-cyan-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition">
                        📨
                    </div>

                    <div class="mt-8 bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition">

                        <span class="text-cyan-600 font-bold text-sm">
                            STEP 02
                        </span>

                        <h3 class="text-2xl font-bold mt-3 mb-4">
                            Invite Members
                        </h3>

                        <p class="text-slate-600 leading-relaxed">
                            Share your unique group code and review member
                            requests directly from your dashboard.
                        </p>

                    </div>

                </div>

                <!-- Step 3 -->
                <div class="group">

                    <div class="w-24 h-24 mx-auto rounded-3xl bg-emerald-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition">
                        💰
                    </div>

                    <div class="mt-8 bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition">

                        <span class="text-emerald-600 font-bold text-sm">
                            STEP 03
                        </span>

                        <h3 class="text-2xl font-bold mt-3 mb-4">
                            Track Savings
                        </h3>

                        <p class="text-slate-600 leading-relaxed">
                            Monitor member contributions, savings growth,
                            penalties and monthly targets automatically.
                        </p>

                    </div>

                </div>

                <!-- Step 4 -->
                <div class="group">

                    <div class="w-24 h-24 mx-auto rounded-3xl bg-purple-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition">
                        📈
                    </div>

                    <div class="mt-8 bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition">

                        <span class="text-purple-600 font-bold text-sm">
                            STEP 04
                        </span>

                        <h3 class="text-2xl font-bold mt-3 mb-4">
                            Manage Loans
                        </h3>

                        <p class="text-slate-600 leading-relaxed">
                            Process loan applications, monitor repayments
                            and keep your group's finances healthy.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- TESTIMONIALS -->
<section id="testimonials" class="py-24 bg-gradient-to-b from-slate-50 to-slate-100">

    <div class="max-w-7xl mx-auto px-6">

        <!-- Header -->
        <div class="text-center mb-16">

            <span class="inline-flex px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                Trusted by Groups Across Kenya
            </span>

            <h2 class="text-5xl font-bold text-slate-900 mt-6">
                What Members Say
            </h2>

            <p class="text-slate-600 mt-4 max-w-2xl mx-auto">
                Real feedback from chama members using ChamaYetu to manage savings, loans, and contributions.
            </p>

        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <!-- Testimonial 1 -->
            <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition">

                <div class="flex items-center gap-4 mb-6">

                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700">
                        JM
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900">James Mwangi</h4>
                        <p class="text-sm text-slate-500">Nairobi Chama Member</p>
                    </div>

                </div>

                <p class="text-slate-600 leading-relaxed">
                    “ChamaYetu has made it so easy for us to track contributions.
                    Everything is transparent and automatic now.”
                </p>

            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition">

                <div class="flex items-center gap-4 mb-6">

                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700">
                        AW
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900">Aisha Wanjiku</h4>
                        <p class="text-sm text-slate-500">Kiambu Savings Group</p>
                    </div>

                </div>

                <p class="text-slate-600 leading-relaxed">
                    “Loan approvals are now fast and transparent. No more confusion in our group meetings.”
                </p>

            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition">

                <div class="flex items-center gap-4 mb-6">

                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center font-bold text-purple-700">
                        DK
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900">David Kiprotich</h4>
                        <p class="text-sm text-slate-500">Eldoret Farmers Chama</p>
                    </div>

                </div>

                <p class="text-slate-600 leading-relaxed">
                    “Managing savings used to be chaotic. Now everything is organized and easy to follow.”
                </p>

            </div>

        </div>

    </div>

</section>

<!-- PRICING -->
<section id="pricing" class="py-24 bg-slate-100">

    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-16">

            <h2 class="text-5xl font-bold">
                Simple Pricing
            </h2>

            <p class="text-slate-600 mt-4">
                Start free and grow with your group.
            </p>

        </div>

        <div class="grid lg:grid-cols-2 gap-8">

            <div class="bg-white rounded-3xl p-10 shadow-lg">

                <h3 class="text-2xl font-bold">
                    Free
                </h3>

                <div class="text-5xl font-black mt-6">
                    KES 0
                </div>

                <ul class="mt-8 space-y-4">

                    <li>✓ Unlimited Members</li>
                    <li>✓ Contributions Tracking</li>
                    <li>✓ Loan Management</li>
                    <li>✓ Reports</li>

                </ul>

                <a href="/register"
                   class="block text-center mt-8 py-4 rounded-xl bg-blue-600 text-white font-bold">

                    Start Free

                </a>

            </div>

            <div class="bg-gradient-to-r from-blue-600 to-teal-500 text-white rounded-3xl p-10 shadow-2xl">

                <span class="bg-white text-blue-600 px-4 py-1 rounded-full text-sm font-bold">
                    Coming Soon
                </span>

                <h3 class="text-2xl font-bold mt-6">
                    Premium
                </h3>

                <div class="text-5xl font-black mt-6">
                    KES 499
                </div>

                <ul class="mt-8 space-y-4">

                    <li>✓ SMS Notifications</li>
                    <li>✓ Advanced Reports</li>
                    <li>✓ M-Pesa Automation</li>
                    <li>✓ Priority Support</li>

                </ul>

            </div>

        </div>

    </div>

</section>

<!-- FAQ -->
<section id="faq" class="py-24 bg-gradient-to-b from-white to-slate-50">

    <div class="max-w-4xl mx-auto px-6">

        <!-- Header -->
        <div class="text-center mb-16">

            <span class="inline-flex px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                Need Help?
            </span>

            <h2 class="text-5xl font-bold text-slate-900 mt-6">
                Frequently Asked Questions
            </h2>

            <p class="text-slate-600 mt-4">
                Everything you need to know about using ChamaYetu
            </p>

        </div>

        <!-- FAQ Items -->
        <div
            x-data="{ open: null }"
            class="space-y-4"
        >

            <!-- Item 1 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">

                <button
                    @click="open === 1 ? open = null : open = 1"
                    class="w-full flex justify-between items-center p-6 text-left hover:bg-slate-50 transition"
                >
                    <span class="font-semibold text-slate-900">
                        Is ChamaYetu free to use?
                    </span>

                    <span class="text-blue-600 text-2xl">
                        +
                    </span>
                </button>

                <div x-show="open === 1" x-transition class="px-6 pb-6 text-slate-600">
                    Yes. You can create groups, manage savings, and track contributions without any subscription fees.
                </div>

            </div>

            <!-- Item 2 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">

                <button
                    @click="open === 2 ? open = null : open = 2"
                    class="w-full flex justify-between items-center p-6 text-left hover:bg-slate-50 transition"
                >
                    <span class="font-semibold text-slate-900">
                        Can I manage loans in my group?
                    </span>

                    <span class="text-blue-600 text-2xl">
                        +
                    </span>
                </button>

                <div x-show="open === 2" x-transition class="px-6 pb-6 text-slate-600">
                    Yes. You can issue loans, track repayments, apply penalties, and generate reports automatically.
                </div>

            </div>

            <!-- Item 3 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">

                <button
                    @click="open === 3 ? open = null : open = 3"
                    class="w-full flex justify-between items-center p-6 text-left hover:bg-slate-50 transition"
                >
                    <span class="font-semibold text-slate-900">
                        Does it work on mobile?
                    </span>

                    <span class="text-blue-600 text-2xl">
                        +
                    </span>
                </button>

                <div x-show="open === 3" x-transition class="px-6 pb-6 text-slate-600">
                    Yes. ChamaYetu is fully responsive and optimized for mobile, tablet, and desktop users.
                </div>

            </div>

            <!-- Item 4 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">

                <button
                    @click="open === 4 ? open = null : open = 4"
                    class="w-full flex justify-between items-center p-6 text-left hover:bg-slate-50 transition"
                >
                    <span class="font-semibold text-slate-900">
                        Can I leave or switch groups?
                    </span>

                    <span class="text-blue-600 text-2xl">
                        +
                    </span>
                </button>

                <div x-show="open === 4" x-transition class="px-6 pb-6 text-slate-600">
                    Yes. Users can switch between approved groups anytime from their dashboard.
                </div>

            </div>

        </div>

    </div>

</section>

<!-- CTA -->
<section class="py-28 bg-slate-50">

    <div class="max-w-6xl mx-auto px-6">

        <div class="relative overflow-hidden rounded-[48px] bg-gradient-to-br from-blue-600 via-blue-500 to-teal-500 text-white shadow-2xl">

            <!-- Glow effects -->
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-black/10 rounded-full blur-3xl"></div>

            <div class="relative p-16 text-center">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 backdrop-blur-md rounded-full text-sm font-semibold mb-6">
                    🚀 Trusted by Chamas Across Kenya
                </div>

                <!-- Heading -->
                <h2 class="text-4xl md:text-5xl font-black leading-tight">
                    Ready to Grow Your Chama?
                </h2>

                <!-- Subtext -->
                <p class="mt-6 text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                    Join hundreds of groups already using ChamaYetu to manage savings, loans, and contributions seamlessly.
                </p>

                <!-- Buttons -->
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">

                    <a href="/register"
                       class="px-10 py-4 bg-white text-blue-600 font-bold rounded-2xl shadow-lg hover:scale-105 transition-transform duration-300">

                        Get Started Free

                    </a>

                    <a href="/login"
                       class="px-10 py-4 border border-white/40 text-white font-semibold rounded-2xl hover:bg-white/10 transition">

                        Sign In

                    </a>

                </div>

                <!-- Trust Row -->
                <div class="mt-12 flex flex-wrap justify-center gap-6 text-white/80 text-sm">

                    <span>✔ No setup fees</span>
                    <span>✔ Secure payments</span>
                    <span>✔ Mobile friendly</span>
                    <span>✔ Instant tracking</span>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- FOOTER -->
<footer class="bg-slate-950 text-slate-400 py-16">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex flex-col lg:flex-row justify-between gap-10">

            <div>

                <h3 class="text-white text-3xl font-bold">
                    ChamaYetu
                </h3>

                <p class="mt-4 max-w-md">
                    Modern savings group management for communities, businesses and investment groups.
                </p>

            </div>

            <div>

                <h4 class="text-white font-semibold mb-4">
                    Quick Links
                </h4>

                <div class="space-y-2">

                    <a href="/login" class="block hover:text-white">
                        Login
                    </a>

                    <a href="/register" class="block hover:text-white">
                        Register
                    </a>

                </div>

            </div>

        </div>

        <div class="border-t border-slate-800 mt-10 pt-8 text-center">

            © {{ date('Y') }} ChamaYetu. All rights reserved.

        </div>

    </div>

</footer>

<script>
document.querySelectorAll('.counter').forEach(counter => {

    const target = +counter.dataset.target;

    const updateCount = () => {

        const count = +counter.innerText;

        const increment = target / 100;

        if (count < target) {

            counter.innerText =
                Math.ceil(count + increment);

            setTimeout(updateCount, 20);

        } else {

            counter.innerText = target;

        }
    };

    updateCount();

});
</script>
<script>

document.querySelectorAll('.faq-btn').forEach(btn => {

    btn.addEventListener('click', () => {

        btn.nextElementSibling.classList.toggle('hidden');

    });

});

</script>

</body>
</html>

