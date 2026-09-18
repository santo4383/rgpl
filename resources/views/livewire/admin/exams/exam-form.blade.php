<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $exam ? __('Edit Exam: ') . $exam->title : __('Create New Exam') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Set exam configuration, duration, passing percentage, and negative marking rules.') }}</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-lg shadow-xs transition">
                &larr; {{ __('Back to Exams') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <form wire:submit="save" class="p-6 sm:p-8 space-y-6">

                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('General Information') }}</h3>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Exam Title') }} *</label>
                            <input type="text" wire:model="title" placeholder="e.g. MPSC State Services Prelims - Full Mock Test 1" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Target Course') }} *</label>
                                <select wire:model.live="course_id" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Course') }}</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Category / Subject (Optional)') }}</label>
                                <select wire:model="category_id" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500" {{ empty($categories) ? 'disabled' : '' }}>
                                    <option value="">{{ __('All Categories / Full Syllabus') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Short Description') }}</label>
                            <textarea wire:model="description" rows="2" placeholder="Brief overview of what this exam tests..." class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('description') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Exam Rules & Scoring -->
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('Scoring & Timing Settings') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Duration (Minutes)') }} *</label>
                                <input type="number" wire:model="duration_minutes" min="1" max="600" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error('duration_minutes') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Total Exam Marks') }} *</label>
                                <input type="number" step="0.5" wire:model="total_marks" min="1" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Assigned questions cannot exceed this.') }}</span>
                                @error('total_marks') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Passing Percentage (%)') }} *</label>
                                <input type="number" step="1" wire:model="pass_percentage" min="1" max="100" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error('pass_percentage') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Negative Marking Section -->
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="has_negative_marking" wire:model.live="has_negative_marking" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="has_negative_marking" class="ml-2 text-xs font-bold text-gray-800 uppercase tracking-wider cursor-pointer">
                                    {{ __('Enable Negative Marking for Wrong Answers') }}
                                </label>
                            </div>

                            @if($has_negative_marking)
                                <div class="pl-6 pt-1 max-w-xs">
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Default Negative Marks per Question') }}</label>
                                    <input type="number" step="0.05" wire:model="default_negative_marks" min="0" placeholder="e.g. 0.25" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="text-[11px] text-rose-600 mt-1 block">{{ __('Deducted for each incorrect answer.') }}</span>
                                    @error('default_negative_marks') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">{{ __('Exam Instructions for Students') }}</label>
                            <textarea wire:model="instructions" rows="4" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <span class="text-[11px] text-gray-500 mt-1 block">{{ __('Displayed in the pre-exam modal before the student clicks "Start Exam".') }}</span>
                            @error('instructions') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Visibility & Premium Controls -->
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2">{{ __('Publication & Access') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-start p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100/70 transition">
                                <input type="checkbox" wire:model="is_published" class="w-4 h-4 mt-0.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-gray-800">{{ __('Published (Visible to Students)') }}</span>
                                    <span class="block text-[11px] text-gray-500">{{ __('When unchecked, the exam remains a draft and is hidden from students.') }}</span>
                                </div>
                            </label>

                            <label class="flex items-start p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100/70 transition">
                                <input type="checkbox" wire:model="is_premium" class="w-4 h-4 mt-0.5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-gray-800">{{ __('Premium Exam Only') }}</span>
                                    <span class="block text-[11px] text-gray-500">{{ __('Requires student to have an active paid subscription covering this course.') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('admin.exams.index') }}" wire:navigate class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-lg transition">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                            {{ $exam ? __('Save Changes') : __('Save & Proceed to Assign Questions →') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
