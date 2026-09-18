<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $question && $question->exists ? __('Edit Question') : __('Create Question') }}
        </h2>
    </x-slot>

    @push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            background-color: #f8fafc;
            border-color: #d1d5db;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            border-color: #d1d5db;
            background-color: #ffffff;
            font-size: 0.95rem;
        }
        .main-editor .ql-editor {
            min-height: 140px;
        }
        .option-editor .ql-editor {
            min-height: 60px;
        }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <div class="font-bold mb-1">{{ __('Please fix the following errors:') }}</div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="save" onsubmit="syncAllQuill()">
                    <!-- Course, Category, Subcategory -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Course <span class="text-red-500">*</span></label>
                            <select wire:model.live="course_id" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Course --</option>
                                @foreach($available_courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Category <span class="text-red-500">*</span></label>
                            <select wire:model.live="category_id" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ empty($available_categories) ? 'disabled' : '' }}>
                                <option value="">-- Select Category --</option>
                                @foreach($available_categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Subcategory (Optional)</label>
                            <select wire:model="subcategory_id" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ empty($available_subcategories) ? 'disabled' : '' }}>
                                <option value="">-- Select Subcategory --</option>
                                @foreach($available_subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                            @error('subcategory_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Question Content Editor -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Question Content <span class="text-red-500">*</span></label>
                        <div wire:ignore class="main-editor">
                            <div id="question-editor">{!! $question_html !!}</div>
                        </div>
                        @error('question_html') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Options -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Options <span class="text-red-500">*</span></h3>
                                <p class="text-xs text-gray-500">Add the choices and mark one radio button as the correct answer.</p>
                            </div>
                            <button type="button" wire:click="addOption" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded shadow-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Option
                            </button>
                        </div>

                        @error('options') <div class="mb-3 text-red-500 text-sm font-semibold">{{ $message }}</div> @enderror

                        @foreach($options as $index => $option)
                            <div class="flex items-start mb-4 bg-white p-4 border border-gray-200 rounded-lg shadow-xs" wire:key="opt-row-{{ $option['uid'] ?? $index }}">
                                <div class="mr-4 mt-2 flex flex-col items-center select-none">
                                    <span class="font-bold text-lg text-gray-700 mb-1.5">{{ $option['option_key'] }}</span>
                                    <input type="radio" name="correct_option_radio" wire:click="setCorrectOption({{ $index }})" {{ !empty($option['is_correct']) ? 'checked' : '' }} class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                                    <span class="text-[11px] font-medium text-green-700 mt-1">Correct</span>
                                </div>
                                <div class="flex-grow min-w-0" wire:ignore>
                                    <div id="option-editor-{{ $option['uid'] ?? $index }}" data-index="{{ $index }}" class="option-editor">{!! $option['option_html'] !!}</div>
                                </div>
                                @if(count($options) > 2)
                                <button type="button" wire:click="removeOption({{ $index }})" class="ml-3 mt-2 text-gray-400 hover:text-red-600 transition" title="Remove option">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Explanation Editor -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Explanation (Optional)</label>
                        <div wire:ignore class="main-editor">
                            <div id="explanation-editor">{!! $explanation_html !!}</div>
                        </div>
                        @error('explanation_html') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Difficulty</label>
                            <select wire:model="difficulty" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Status</label>
                            <select wire:model="status" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="DRAFT">Draft</option>
                                <option value="REVIEW">Needs Review</option>
                                <option value="PUBLISHED">Published</option>
                            </select>
                        </div>
                        <div>
                            <label class="flex items-center mt-8 cursor-pointer">
                                <input type="checkbox" wire:model="is_free" class="form-checkbox h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Is Free Question?</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded shadow transition">
                            Save Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        window.qEditor = null;
        window.eEditor = null;
        window.optEditors = {};

        const fullToolbar = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean']
        ];

        const simpleToolbar = [
            ['bold', 'italic', 'underline'],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['image'],
            ['clean']
        ];

        function initQuill() {
            if (typeof Quill === 'undefined') {
                setTimeout(initQuill, 100);
                return;
            }

            // 1. Question Editor
            const qEl = document.getElementById('question-editor');
            if (qEl && !qEl.classList.contains('ql-container')) {
                // Remove any stray duplicate toolbar
                while (qEl.previousElementSibling && qEl.previousElementSibling.classList.contains('ql-toolbar')) {
                    qEl.previousElementSibling.remove();
                }
                window.qEditor = new Quill(qEl, { theme: 'snow', modules: { toolbar: fullToolbar } });
                window.qEditor.on('text-change', function() {
                    @this.set('question_html', window.qEditor.root.innerHTML, false);
                });
            }

            // 2. Explanation Editor
            const eEl = document.getElementById('explanation-editor');
            if (eEl && !eEl.classList.contains('ql-container')) {
                while (eEl.previousElementSibling && eEl.previousElementSibling.classList.contains('ql-toolbar')) {
                    eEl.previousElementSibling.remove();
                }
                window.eEditor = new Quill(eEl, { theme: 'snow', modules: { toolbar: fullToolbar } });
                window.eEditor.on('text-change', function() {
                    @this.set('explanation_html', window.eEditor.root.innerHTML, false);
                });
            }

            // 3. Option Editors
            document.querySelectorAll('.option-editor').forEach(el => {
                if (!el.classList.contains('ql-container')) {
                    while (el.previousElementSibling && el.previousElementSibling.classList.contains('ql-toolbar')) {
                        el.previousElementSibling.remove();
                    }
                    const idx = el.getAttribute('data-index');
                    const optQuill = new Quill(el, { theme: 'snow', modules: { toolbar: simpleToolbar } });
                    window.optEditors[idx] = optQuill;
                    optQuill.on('text-change', function() {
                        @this.set('options.' + idx + '.option_html', optQuill.root.innerHTML, false);
                    });
                }
            });
        }

        window.syncAllQuill = function() {
            if (window.qEditor) {
                @this.set('question_html', window.qEditor.root.innerHTML, false);
            }
            if (window.eEditor) {
                @this.set('explanation_html', window.eEditor.root.innerHTML, false);
            }
            document.querySelectorAll('.option-editor').forEach(el => {
                const idx = el.getAttribute('data-index');
                if (window.optEditors && window.optEditors[idx]) {
                    @this.set('options.' + idx + '.option_html', window.optEditors[idx].root.innerHTML, false);
                }
            });
        };

        // Initialize once DOM is ready or after Livewire navigates
        let quillInitTimeout = null;
        function scheduleInitQuill() {
            clearTimeout(quillInitTimeout);
            quillInitTimeout = setTimeout(initQuill, 50);
        }

        document.addEventListener('livewire:navigated', scheduleInitQuill);
        document.addEventListener('DOMContentLoaded', scheduleInitQuill);

        // When Livewire modifies DOM (e.g. adding an option), initialize any newly added editors
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', () => {
                scheduleInitQuill();
            });
        });
    </script>
    @endpush
</div>
