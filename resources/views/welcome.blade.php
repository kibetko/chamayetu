<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChamaYetu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: '#1B424C',
                        green: '#39C260',
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-white text-gray-900">

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-white shadow-xs px-16 h-16 flex items-center justify-between gap-4 ">
    <div class="flex items-center ">
        <span class="text-2xl font-bold text-teal">Chama</span>
        <span class="text-2xl font-bold text-green">Yetu</span>
    </div>

    <ul class="hidden md:flex items-center gap-8 list-none">
        <li><a href="#home" class="text-gray-800 font-medium hover:text-teal transition-colors">Home</a></li>
        <li><a href="#services" class="text-gray-800 font-medium hover:text-teal transition-colors">Services</a></li>
        <li><a href="#how-it-works" class="text-gray-800 font-medium hover:text-teal transition-colors">How we work?</a></li>
        <li><a href="#about" class="text-gray-800 font-medium hover:text-teal transition-colors">About Us</a></li>
    </ul>

    <div class="hidden md:flex items-center gap-3">
        <a href="/login" class="px-5 py-1.5 rounded-full border-2 border-teal text-teal font-semibold text-sm hover:bg-teal hover:text-white transition-all">Login</a>
        <a href="#contact" class="px-5 py-1.5 rounded-full bg-teal text-white font-semibold text-sm hover:bg-opacity-90 transition-all">Contact us</a>
    </div>

    <button id="menu-btn" class="md:hidden text-2xl bg-transparent border-none cursor-pointer">☰</button>
</nav>

<!-- MOBILE MENU -->
<div id="mobile-menu" class="hidden flex-col gap-4 px-6 py-4 bg-white border-t border-gray-100 md:hidden">
    <a href="#home" class="text-gray-800 font-medium">Home</a>
    <a href="#services" class="text-gray-800 font-medium">Services</a>
    <a href="#how-it-works" class="text-gray-800 font-medium">How we work?</a>
    <a href="#about" class="text-gray-800 font-medium">About Us</a>
    <a href="/login" class="text-teal font-semibold">Login</a>
    <a href="#contact" class="bg-teal text-white px-4 py-2 rounded-full text-center font-semibold">Contact us</a>
</div>

<!-- HERO -->
<section id="home" class="flex flex-col md:flex-row items-center justify-between gap-10 md:gap-40 px-16 py-20 bg-gray-50">
    <div class="max-w-xl">
        <h2 class="font-serif text-3xl xl:text-5xl leading-tight text-gray-900">
            Quick and Easy Loans for Your Financial Needs.
        </h2>
        <p class="mt-5 text-base text-gray-500 leading-relaxed">
            Our loan services offer a hassle-free and streamlined borrowing experience, providing you with the funds you need in a timely manner to meet your financial requirements.
        </p>
        <a href="/register" class="inline-block mt-8 bg-white text-teal font-semibold  px-7 py-3 rounded-3xl hover:bg-opacity-90 transition-all border-2 border-teal">
            Get started
        </a>
    </div>

    <div class=" max-w-xs  xl:max-w-5xl flex-shrink-0">
        <img src="{{ asset('landingpage/illustartion.svg') }}" alt="Hero illustration" />
    </div>
</section>

<!-- SERVICES -->
<section id="services" class="px-16 py-20" style="background: linear-gradient(to right, #C9E4DE 5%, #CEE4E0 45%, #D0E4E0 51%, #DFE3E7 100%);">
    <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>

    <div class="grid md:grid-cols-3 gap-6">

        <div class="bg-white/40 rounded-2xl p-8 text-center hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="w-16 h-16 mx-auto mb-4 rounded-sm flex items-center justify-center">
                <img src="{{ asset('landingpage/vector.svg') }}" alt="Personal loan" class="w-14 h-14" />
            </div>
            <h3 class="text-lg font-bold mb-2">Personal loan</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-5">Personal loans provide borrowers with flexibility in how they use the funds.</p>
            <a href="/register" class="inline-block px-6 py-2 rounded-full border-2 border-teal text-teal text-sm font-semibold hover:bg-teal hover:text-white transition-all">Apply now</a>
        </div>

        <div class="bg-white/40 rounded-2xl p-8 text-center hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center">
                <img src="{{ asset('landingpage/Group 5.svg') }}" alt="Business loan" class="w-14 h-14" />
            </div>
            <h3 class="text-lg font-bold mb-2">Business loan</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-5">Business Loan Services provide financial assistance to businesses for various purposes.</p>
            <a href="/register" class="inline-block px-6 py-2 rounded-full border-2 border-teal text-teal text-sm font-semibold hover:bg-teal hover:text-white transition-all">Apply now</a>
        </div>

        <div class="bg-white/40 rounded-2xl p-8 text-center hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center">
                <img src="{{ asset('landingpage/Group 6.svg') }}" alt="Auto loan" class="w-14 h-14" />
            </div>
            <h3 class="text-lg font-bold mb-2">Auto loan</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-5">Auto Loan Services provide financing options for individuals and businesses to purchase a vehicle.</p>
            <a href="/register" class="inline-block px-6 py-2 rounded-full border-2 border-teal text-teal text-sm font-semibold hover:bg-teal hover:text-white transition-all">Apply now</a>
        </div>

    </div>

    <div class="text-center mt-10">
        <a href="#" class="inline-block px-8 py-3 bg-teal text-white rounded-full font-semibold hover:bg-opacity-90 transition-all">View more</a>
    </div>
</section>

<!-- HOW WE WORK -->
<section id="how-it-works" class="px-16 py-20 bg-teal/5">
    <h2 class="text-3xl font-bold text-center">How we works ?</h2>
    <p class="text-center text-gray-500 mt-2 mb-16">This is a process, how you can get loan for your self.</p>

    <div class="flex flex-col gap-16">

        <!-- Step 1 -->
        <div class="flex items-center gap-16">
            <!-- IMAGE PLACEHOLDER -->
            <div class="flex-shrink-0 w-52 h-52 rounded-full  flex flex-col items-center justify-center border-2 border-dashed border-green/40 text-center p-4">
                <img src="{{ asset('landingpage/card1.svg') }}" alt="Step 1" class="w-40 h-40" />
            </div>
            <div class="flex-1">
                <p class="font-serif text-6xl font-bold text-teal/10 leading-none mb-[-10px]">01</p>
                <h3 class="text-xl font-bold mb-3">Application</h3>
                <p class="text-gray-500 leading-relaxed">The borrower submits a loan application to the bank, either in person, online, or through other channels. The application includes personal and financial information, such as income, employment history, credit score, and the purpose of the loan.</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="flex flex-row-reverse items-center gap-16">
            <!-- IMAGE PLACEHOLDER -->
            <div class="flex-shrink-0 w-52 h-52 rounded-full bg-green/10 flex flex-col items-center justify-center border-2 border-dashed border-green/40 text-center p-4">
                <svg class="w-8 h-8 text-green/50 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <p class="text-xs text-green/60 font-medium">Step Image</p>
            </div>
            <div class="flex-1">
                <p class="font-serif text-6xl font-bold text-teal/10 leading-none mb-[-10px]">02</p>
                <h3 class="text-xl font-bold mb-3">Documentation and Verification</h3>
                <p class="text-gray-500 leading-relaxed">The bank requests supporting documents from the borrower, such as identification proof, income statements, bank statements, and collateral details (if applicable). The bank verifies the information provided to assess the borrower's creditworthiness and eligibility for the loan.</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="flex items-center gap-16">
            <!-- IMAGE PLACEHOLDER -->
            <div class="flex-shrink-0 w-52 h-52 rounded-full bg-green/10 flex flex-col items-center justify-center border-2 border-dashed border-green/40 text-center p-4">
                <svg class="w-8 h-8 text-green/50 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <p class="text-xs text-green/60 font-medium">Step Image</p>
            </div>
            <div class="flex-1">
                <p class="font-serif text-6xl font-bold text-teal/10 leading-none mb-[-10px]">03</p>
                <h3 class="text-xl font-bold mb-3">Credit Assessment</h3>
                <p class="text-gray-500 leading-relaxed">The bank conducts a credit assessment to evaluate the borrower's creditworthiness and ability to repay the loan. This process involves analyzing the borrower's credit history, income stability, debt-to-income ratio, and other factors.</p>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="flex flex-row-reverse items-center gap-16">
            <!-- IMAGE PLACEHOLDER -->
            <div class="flex-shrink-0 w-52 h-52 rounded-full bg-green/10 flex flex-col items-center justify-center border-2 border-dashed border-green/40 text-center p-4">
                <svg class="w-8 h-8 text-green/50 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <p class="text-xs text-green/60 font-medium">Step Image</p>
            </div>
            <div class="flex-1">
                <p class="font-serif text-6xl font-bold text-teal/10 leading-none mb-[-10px]">04</p>
                <h3 class="text-xl font-bold mb-3">Loan Approval</h3>
                <p class="text-gray-500 leading-relaxed">If the borrower meets the bank's lending criteria and passes the credit assessment, the loan is approved. The bank determines the loan amount, interest rate, repayment term, and any associated fees.</p>
            </div>
        </div>

    </div>
</section>

<!-- ABOUT + CONTACT FORM -->
<section id="about" class="px-16 py-20 bg-white flex gap-16 items-start">

    <!-- IMAGE PLACEHOLDER -->
    <div class="hidden lg:flex flex-shrink-0 w-52 h-52 rounded-full bg-green/10 flex-col items-center justify-center border-2 border-dashed border-green/40 text-center p-4">
        <svg class="w-8 h-8 text-green/50 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
        <p class="text-xs text-green/60 font-medium">About Image</p>
    </div>

    <div class="flex-1">
        <h2 class="text-3xl font-bold mb-5">About us</h2>
        <p class="text-gray-500 leading-relaxed">QuickFunds — Your trusted financial partner for loans. Quick approvals, competitive rates, and personalized solutions to meet your unique needs. Empowering you to achieve your financial goals. Apply online today!</p>
        <p class="text-gray-500 leading-relaxed mt-4">Our mission is to empower individuals and businesses by providing them with the financial resources they need to achieve their goals.</p>
    </div>

    <!-- CONTACT FORM -->
    <div id="contact" class="flex-shrink-0 w-80">
        <h3 class="text-xl font-bold mb-5">Get in touch</h3>
        <input type="text" placeholder="Your Name"
               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm mb-3 outline-none focus:border-teal transition-colors" />
        <input type="tel" placeholder="Phone number"
               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm mb-3 outline-none focus:border-teal transition-colors" />
        <input type="email" placeholder="Email address"
               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm mb-4 outline-none focus:border-teal transition-colors" />
        <button class="w-full py-3 bg-teal text-white font-bold rounded-lg hover:bg-opacity-90 transition-all">SEND</button>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-[#0d2b33] text-gray-400 px-16 py-14 grid grid-cols-1 md:grid-cols-3 gap-10">

    <div>
        <div class="flex items-center">
            <span class="text-2xl font-bold text-white">Chama</span>
            <span class="text-2xl font-bold text-green">Yetu</span>
        </div>
        <p class="mt-4 text-sm leading-relaxed text-gray-500">Our mission is to empower individuals and businesses by providing them with the financial resources they need to achieve their goals.</p>
        <div class="flex gap-2 mt-5">
            <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs hover:bg-green transition-colors">f</a>
            <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs hover:bg-green transition-colors">in</a>
            <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs hover:bg-green transition-colors">tw</a>
            <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs hover:bg-green transition-colors">yt</a>
            <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs hover:bg-green transition-colors">ig</a>
        </div>
    </div>

    <div>
        <h4 class="text-white font-bold uppercase tracking-widest text-sm mb-4">Our Services</h4>
        <ul class="space-y-2">
            <li><a href="#" class="text-sm hover:text-green transition-colors">Personal loan</a></li>
            <li><a href="#" class="text-sm hover:text-green transition-colors">Business loan</a></li>
            <li><a href="#" class="text-sm hover:text-green transition-colors">Education loan</a></li>
            <li><a href="#" class="text-sm hover:text-green transition-colors">Auto loan</a></li>
        </ul>
    </div>

    <div>
        <h4 class="text-white font-bold uppercase tracking-widest text-sm mb-4">Contact Us</h4>
        <p class="flex items-center gap-2 text-sm mb-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            +254 700 000 000
        </p>
        <p class="flex items-center gap-2 text-sm mb-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            support@chamayetu.com
        </p>
        <p class="flex items-start gap-2 text-sm">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Nairobi, Kenya
        </p>
    </div>

</footer>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
</script>

</body>
</html>