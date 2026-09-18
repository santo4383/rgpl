<x-public-layout title="Contact Us - RGPL Academy" description="Get in touch with RGPL Academy student support, academic faculty, and subscription verification desk.">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-slate-900 via-blue-950 to-indigo-950 text-white py-16 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Help &amp; Inquiries
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                Contact Our Student Desk
            </h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-xl mx-auto leading-relaxed">
                Have inquiries regarding course syllabus, payments, or question errata? Send us a message and our team will assist you.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left 5 Cols: Contact Information -->
            <div class="lg:col-span-5 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Get in Touch</h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        We are available Monday to Saturday, 9:00 AM to 7:00 PM IST to address student questions and payment confirmations.
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Phone -->
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phone &amp; WhatsApp</div>
                            <div class="text-base font-bold text-slate-900 mt-0.5">{{ $contactPhone }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Direct Helpline for Aspirants</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email Address</div>
                            <div class="text-base font-bold text-slate-900 mt-0.5"><a href="mailto:{{ $contactEmail }}" class="hover:text-blue-700">{{ $contactEmail }}</a></div>
                            <div class="text-xs text-slate-500 mt-0.5">Guaranteed response within 24 hours</div>
                        </div>
                    </div>

                    <!-- Office Address -->
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Office Location</div>
                            <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $contactAddress }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Maharashtra, India</div>
                        </div>
                    </div>
                </div>

                <!-- Support hours card -->
                <div class="p-6 rounded-2xl bg-slate-900 text-slate-300 text-xs space-y-2">
                    <div class="text-white font-bold text-sm">Operating Hours</div>
                    <p>Monday – Friday: 9:00 AM – 7:00 PM</p>
                    <p>Saturday: 10:00 AM – 4:00 PM</p>
                    <p>Sunday &amp; Public Holidays: Closed (Emergency UTR inquiries answered via email)</p>
                </div>
            </div>

            <!-- Right 7 Cols: Contact Form -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-md">
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Send Us a Message</h2>
                <p class="text-sm text-slate-600 mb-8">Fill in your information below and our team will get back to you promptly.</p>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                        @foreach($errors->all() as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ url('/contact-us') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Honeypot Field (hidden from genuine users) -->
                    <div class="hidden" aria-hidden="true">
                        <label for="website_hp">Do not fill this field:</label>
                        <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required value="{{ old('name', auth()->user()?->name) }}" placeholder="e.g. Rahul Deshmukh" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mobile" id="mobile" required value="{{ old('mobile', auth()->user()?->mobile_number) }}" placeholder="e.g. 9876543210" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="e.g. rahul@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}" placeholder="e.g. Inquiry regarding MPSC Prelims Pass or UTR Activation" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="5" required placeholder="Describe your question, query, or feedback in detail..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600">{{ old('message') }}</textarea>
                    </div>

                    <div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-blue-700 hover:bg-blue-800 shadow-md hover:shadow-lg transition">
                            Send Message &rarr;
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-public-layout>
