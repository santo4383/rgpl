<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Homepage Sections & Layout Control</h1>
                <p class="text-sm text-gray-500 mt-1">Configure visibility of the 16 homepage sections and manage featured exam streams and categories.</p>
            </div>
            <button wire:click="saveSections" class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Section Settings
            </button>
        </div>

        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- 16-Section Visibility Configuration -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-lg font-bold text-gray-900">Homepage Section Visibility Toggles</h2>
                <p class="text-xs text-gray-500 mt-1">Enable or disable specific sections from appearing on the public homepage. Click "Save Section Settings" to commit.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 1. Top Announcement Bar -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">1. Top Announcement Bar</div>
                        <div class="text-xs text-gray-500">Displays urgent ticker alerts at the very top of all public pages</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_announcement_bar" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 2. Hero Slider -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">2. Hero Slider &amp; Quick Practice</div>
                        <div class="text-xs text-gray-500">Carousel banners with headline, CTA, and instant trial drawer</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_hero_slider" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 3. Target Exams & Streams -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">3. Examination Streams / Courses</div>
                        <div class="text-xs text-gray-500">Cards for MPSC, Police Bharti, Commerce (11th, 12th, CA, CS, ICWA)</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_courses" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 4. Platform At a Glance Stats -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">4. Platform at a Glance</div>
                        <div class="text-xs text-gray-500">Counter stats (50,000+ Questions, Mock Tests, Accuracy metrics)</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_glance" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 5. Practice vs Exam Mode Comparison -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">5. Practice vs. Exam Mode</div>
                        <div class="text-xs text-gray-500">Comparison cards showing untimed practice vs. timed negative marking</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_practice_exam" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 6. Why Choose Us -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">6. Why Choose ParikshaMitra</div>
                        <div class="text-xs text-gray-500">Feature highlights: Bilingual (Marathi/English), AI Analytics, Detailed Explanations</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_why_us" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 7. How It Works -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">7. How It Works (4 Steps)</div>
                        <div class="text-xs text-gray-500">Select Stream &rarr; Choose Mode &rarr; Attempt MCQs &rarr; Review Analytics</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_how_it_works" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 8. Free Question Bank CTA -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">8. Free Practice Callout</div>
                        <div class="text-xs text-gray-500">Promotion for 500+ free practice questions without credit card</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_free_cta" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 9. Featured Categories -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">9. Featured Categories Grid</div>
                        <div class="text-xs text-gray-500">Displays categories marked as "is_featured" across all streams</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_featured_courses" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 10. Latest Announcements & News -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">10. Exam Updates &amp; News</div>
                        <div class="text-xs text-gray-500">Latest published notifications, syllabus updates, and articles</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_news" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 11. Student Testimonials -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">11. Student Testimonials</div>
                        <div class="text-xs text-gray-500">Reviews &amp; success stories from aspirants</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_testimonials" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 12. Transparent Pricing -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">12. Transparent Pricing Plans</div>
                        <div class="text-xs text-gray-500">Monthly, Half-Yearly, and Yearly pass subscriptions</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_pricing" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 13. FAQ Accordion -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">13. Frequently Asked Questions</div>
                        <div class="text-xs text-gray-500">Interactive Q&amp;A accordion for common student inquiries</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_faq" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- 14. Final Registration Banner -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">14. Final Enrollment CTA</div>
                        <div class="text-xs text-gray-500">High-converting call to action before the footer</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="section_final_cta" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button wire:click="saveSections" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    Save Section Visibility
                </button>
            </div>
        </div>

        <!-- Featured Streams & Categories Selection -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-lg font-bold text-gray-900">Featured Streams &amp; Categories Configuration</h2>
                <p class="text-xs text-gray-500 mt-1">Toggle which Courses and Categories appear in the homepage highlight sections. These update instantly.</p>
            </div>

            <!-- Examination Fields (Courses) -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Examination Streams (Courses)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($allCourses as $c)
                        <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 hover:border-indigo-200 transition bg-white">
                            <div>
                                <div class="font-bold text-gray-900 text-sm">{{ $c->name }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">/{{ $c->slug }}</div>
                            </div>
                            <button wire:click="toggleCourseFeatured({{ $c->id }})" class="px-3 py-1 text-xs font-bold rounded-lg transition {{ $c->is_featured ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-gray-100 text-gray-600' }}">
                                {{ $c->is_featured ? 'Featured' : 'Standard' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Categories -->
            <div class="pt-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Categories (Subjects / Exam Branches)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($allCategories as $cat)
                        <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 hover:border-indigo-200 transition bg-white">
                            <div class="truncate mr-2">
                                <div class="font-bold text-gray-900 text-sm truncate">{{ $cat->name }}</div>
                                <div class="text-[11px] text-indigo-600 font-semibold truncate">{{ $cat->course?->name }}</div>
                            </div>
                            <button wire:click="toggleCategoryFeatured({{ $cat->id }})" class="px-3 py-1 text-xs font-bold rounded-lg shrink-0 transition {{ $cat->is_featured ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-gray-100 text-gray-600' }}">
                                {{ $cat->is_featured ? 'Featured' : 'Standard' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
