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
        .math-palette {
            display: flex; flex-wrap: wrap; gap: 3px; padding: 6px;
            border: 1px solid #d1d5db; border-bottom: 0;
            border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem;
            background: #eef2ff;
        }
        .math-palette + .ql-toolbar.ql-snow { border-top-left-radius: 0; border-top-right-radius: 0; }
        .math-palette button {
            min-width: 28px; height: 26px; padding: 0 6px; font-size: 14px; line-height: 1;
            border: 1px solid #c7d2fe; border-radius: 5px; background: #fff; color: #312e81;
        }
        .math-palette button:hover { background: #e0e7ff; border-color: #6366f1; }
        .math-palette button.math-tpl { font-size: 12px; font-weight: 600; background: #f5f3ff; }
        .math-palette-sep { width: 1px; background: #c7d2fe; margin: 0 4px; }
        .ql-editor .ql-formula { cursor: pointer; padding: 0 2px; border-radius: 3px; }
        .ql-editor .ql-formula:hover { background: #eef2ff; outline: 1px dashed #818cf8; }
        .ql-snow .ql-tooltip[data-mode=formula] { display: none !important; }
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
                    <!-- Auto-Generated Question Info (Non-editable) -->
                    <div class="mb-6 bg-slate-50 border border-slate-200 rounded-lg p-4 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Question Code (Non-Editable)</span>
                            <span class="text-lg font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded inline-block mt-1 select-none">
                                {{ $questionDetails['code'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-6">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Sequence No.</span>
                                <span class="text-sm font-bold text-slate-800 mt-1 block select-none">
                                    {{ $questionDetails['number'] ? '#'.$questionDetails['number'] : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Total Course Questions</span>
                                <span class="text-sm font-bold text-slate-800 mt-1 block select-none">
                                    {{ $questionDetails['total_course_questions'] }} {{ Str::plural('question', $questionDetails['total_course_questions']) }}
                                </span>
                            </div>
                        </div>
                    </div>

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
                            <select wire:model.live="subcategory_id" class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ empty($available_subcategories) ? 'disabled' : '' }}>
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
                                <p class="text-xs text-gray-500">Add the choices and check the checkbox(es) for all correct answers (multiple correct options allowed).</p>
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
                                    <input type="checkbox" wire:model="options.{{ $index }}.is_correct" class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer">
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

    <!-- Math Formula Dialog (shared by all editors) -->
    <div id="math-formula-dialog" wire:ignore onclick="if(event.target === this) window.mathFormulaModal.close()" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-200">
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">ƒx</span>
                    <h3 class="font-bold text-gray-800 text-base" id="math-dialog-title">{{ __('Insert / Edit Math Formula (LaTeX)') }}</h3>
                </div>
                <button type="button" onclick="window.mathFormulaModal.close()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200 flex items-center justify-center text-xl font-bold leading-none transition">&times;</button>
            </div>
            
            <div class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                <!-- Quick Insertion Templates -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Quick Templates (Click to insert):') }}</span>
                        <span class="text-[11px] text-gray-400 font-medium">{{ __('Format: Math Formula (Formula Name)') }}</span>
                    </div>

                    <!-- Category filter tabs -->
                    <div class="flex items-center gap-1 mb-2 overflow-x-auto pb-0.5 text-xs">
                        <button type="button" onclick="window.mathFormulaModal.filterTemplates('all', this)" class="math-cat-btn px-2.5 py-0.5 rounded-full font-semibold bg-indigo-600 text-white shadow-xs transition">All</button>
                        <button type="button" onclick="window.mathFormulaModal.filterTemplates('basic', this)" class="math-cat-btn px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Basic &amp; Roots</button>
                        <button type="button" onclick="window.mathFormulaModal.filterTemplates('powers', this)" class="math-cat-btn px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Exponents &amp; Powers</button>
                        <button type="button" onclick="window.mathFormulaModal.filterTemplates('algebra', this)" class="math-cat-btn px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Algebra &amp; Geometry</button>
                        <button type="button" onclick="window.mathFormulaModal.filterTemplates('advanced', this)" class="math-cat-btn px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Trig &amp; Calculus</button>
                    </div>

                    @php
                    $dialogTemplates = [
                        ['cat' => 'basic', 'label' => 'a/b (Fraction)', 'latex' => '\frac{a}{b}', 'title' => 'Fraction'],
                        ['cat' => 'basic', 'label' => 'a b/c (Mixed Fraction)', 'latex' => 'a\frac{b}{c}', 'title' => 'Mixed Fraction'],
                        ['cat' => 'basic', 'label' => '√x (Square Root)', 'latex' => '\sqrt{x}', 'title' => 'Square root'],
                        ['cat' => 'basic', 'label' => '∛x (Cube Root)', 'latex' => '\sqrt[3]{x}', 'title' => 'Cube root'],
                        ['cat' => 'basic', 'label' => 'ⁿ√x (Nth Root)', 'latex' => '\sqrt[n]{x}', 'title' => 'Nth root'],
                        ['cat' => 'basic', 'label' => 'xⁿ (Power)', 'latex' => 'x^{n}', 'title' => 'Exponent / Power'],
                        ['cat' => 'basic', 'label' => 'xₙ (Subscript)', 'latex' => 'x_{n}', 'title' => 'Subscript'],
                        ['cat' => 'basic', 'label' => 'xₙᵐ (Subscript & Power)', 'latex' => 'x_{n}^{m}', 'title' => 'Subscript and power'],
                        ['cat' => 'basic', 'label' => '|x| (Absolute Value)', 'latex' => '\left| x \right|', 'title' => 'Absolute value'],
                        ['cat' => 'basic', 'label' => 'a:b (Ratio)', 'latex' => 'a : b', 'title' => 'Ratio'],
                        ['cat' => 'basic', 'label' => 'a% (Percentage)', 'latex' => 'a\%', 'title' => 'Percentage'],

                        ['cat' => 'powers', 'label' => '√(a+b)² (Root of Expression)', 'latex' => '\sqrt{(a+b)^{2}}', 'title' => 'Root of expression'],
                        ['cat' => 'powers', 'label' => '∛24389 (Cube Root of Number)', 'latex' => '\sqrt[3]{24389}', 'title' => 'Cube root of number'],
                        ['cat' => 'powers', 'label' => '[(2⁴)¹⁄²]¹ (Nested Powers)', 'latex' => '\left[\left(2^{4}\right)^{1/2}\right]^{1}', 'title' => 'Nested powers'],
                        ['cat' => 'powers', 'label' => '(2²×2³)/(2²×2³) (Fraction with Powers)', 'latex' => '\frac{2^{2} \times 2^{3}}{2^{2} \times 2^{3}}', 'title' => 'Fraction with powers'],
                        ['cat' => 'powers', 'label' => '(1/216)⁻²⁄³ + (1/256)⁻³⁄⁴ + (1/243)⁻¹⁄³ (Fraction Powers Sum)', 'latex' => '\left(\frac{1}{216}\right)^{-2/3} + \left(\frac{1}{256}\right)^{-3/4} + \left(\frac{1}{243}\right)^{-1/3}', 'title' => 'Sum of fraction powers'],
                        ['cat' => 'powers', 'label' => '(a/b)ⁿ (Fraction to Power)', 'latex' => '\left(\frac{a}{b}\right)^{n}', 'title' => 'Fraction to power'],
                        ['cat' => 'powers', 'label' => 'x⁻ⁿ (Negative Exponent)', 'latex' => 'x^{-n}', 'title' => 'Negative exponent'],

                        ['cat' => 'algebra', 'label' => '(a+b)² (Square of Sum)', 'latex' => '(a+b)^{2} = a^{2} + 2ab + b^{2}', 'title' => 'Square of sum'],
                        ['cat' => 'algebra', 'label' => '(a-b)² (Square of Difference)', 'latex' => '(a-b)^{2} = a^{2} - 2ab + b^{2}', 'title' => 'Square of difference'],
                        ['cat' => 'algebra', 'label' => 'a² - b² (Difference of Squares)', 'latex' => 'a^{2} - b^{2} = (a-b)(a+b)', 'title' => 'Difference of squares'],
                        ['cat' => 'algebra', 'label' => '(a+b)³ (Cube of Sum)', 'latex' => '(a+b)^{3} = a^{3} + 3a^{2}b + 3ab^{2} + b^{3}', 'title' => 'Cube of sum'],
                        ['cat' => 'algebra', 'label' => 'x = (-b±√(b²-4ac))/2a (Quadratic Formula)', 'latex' => 'x = \frac{-b \pm \sqrt{b^{2}-4ac}}{2a}', 'title' => 'Quadratic formula'],
                        ['cat' => 'algebra', 'label' => 'a² + b² = c² (Pythagorean Theorem)', 'latex' => 'a^{2} + b^{2} = c^{2}', 'title' => 'Pythagorean theorem'],
                        ['cat' => 'algebra', 'label' => '∠ABC = 90° (Angle Degree)', 'latex' => '\angle ABC = 90^{\circ}', 'title' => 'Angle degree'],
                        ['cat' => 'algebra', 'label' => 'πr² (Area of Circle)', 'latex' => 'A = \pi r^{2}', 'title' => 'Area of circle'],
                        ['cat' => 'algebra', 'label' => '2πr (Circumference)', 'latex' => 'C = 2\pi r', 'title' => 'Circumference'],

                        ['cat' => 'advanced', 'label' => 'sin²θ + cos²θ = 1 (Trig Identity)', 'latex' => '\sin^{2}\theta + \cos^{2}\theta = 1', 'title' => 'Trigonometric identity'],
                        ['cat' => 'advanced', 'label' => 'tan θ = sin θ / cos θ (Tangent Ratio)', 'latex' => '\tan\theta = \frac{\sin\theta}{\cos\theta}', 'title' => 'Tangent ratio'],
                        ['cat' => 'advanced', 'label' => 'x̄ = ∑xᵢ/n (Arithmetic Mean)', 'latex' => '\bar{x} = \frac{\sum x_{i}}{n}', 'title' => 'Arithmetic mean'],
                        ['cat' => 'advanced', 'label' => '∑ᵢ₌₁ⁿ xᵢ (Summation Series)', 'latex' => '\sum_{i=1}^{n} x_{i}', 'title' => 'Summation series'],
                        ['cat' => 'advanced', 'label' => 'lim(x→0) (Limit Function)', 'latex' => '\lim_{x \to 0} f(x)', 'title' => 'Limit function'],
                        ['cat' => 'advanced', 'label' => 'dy/dx (Derivative)', 'latex' => '\frac{dy}{dx}', 'title' => 'Derivative'],
                        ['cat' => 'advanced', 'label' => '∫ f(x)dx (Integral)', 'latex' => '\int f(x) \, dx', 'title' => 'Integral'],
                        ['cat' => 'advanced', 'label' => '[a b; c d] (2×2 Matrix)', 'latex' => '\begin{bmatrix} a & b \\\\ c & d \end{bmatrix}', 'title' => '2x2 Matrix'],
                    ];
                    @endphp

                    <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl bg-slate-50/70" id="math-dialog-snippets">
                        @foreach($dialogTemplates as $tpl)
                            <button type="button"
                                    data-category="{{ $tpl['cat'] }}"
                                    data-latex="{{ $tpl['latex'] }}"
                                    onclick="window.mathFormulaModal.insertSnippet(this.getAttribute('data-latex'))"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-md border border-gray-300 bg-white hover:bg-indigo-50 hover:border-indigo-400 transition"
                                    title="{{ $tpl['title'] }}">
                                {{ $tpl['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- LaTeX Input Textarea -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            {{ __('Formula LaTeX Syntax:') }}
                            <span class="text-gray-400 font-normal lowercase">({{ __('type, paste, or edit below') }})</span>
                        </label>
                        <button type="button"
                                id="math-dialog-clear"
                                onclick="window.mathFormulaModal.clear()"
                                class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-2.5 py-1 rounded-md flex items-center gap-1.5 transition border border-rose-200"
                                title="{{ __('Clear formula input') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>{{ __('Clear Formula') }}</span>
                        </button>
                    </div>
                    <textarea id="math-dialog-input" rows="3" spellcheck="false"
                              oninput="window.mathFormulaModal.preview()"
                              onkeydown="if(event.key === 'Enter' && (event.ctrlKey || event.metaKey)){ event.preventDefault(); window.mathFormulaModal.save(); } if(event.key === 'Escape'){ window.mathFormulaModal.close(); } if(event.altKey && (event.key === 'c' || event.key === 'C')){ event.preventDefault(); window.mathFormulaModal.clear(); }"
                              class="w-full font-mono text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 shadow-xs"
                              placeholder="e.g. \sqrt{(a+b)^{2}} or \frac{a}{b}"></textarea>
                    <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-between">
                        <span>{{ __('Tip: Press') }} <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 border border-gray-300 rounded">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 border border-gray-300 rounded">Enter</kbd> {{ __('to insert immediately.') }}</span>
                        <span class="text-gray-400">{{ __('Use') }} <kbd class="px-1 py-0.5 text-[10px] bg-gray-100 border border-gray-300 rounded">Alt</kbd>+<kbd class="px-1 py-0.5 text-[10px] bg-gray-100 border border-gray-300 rounded">C</kbd> {{ __('or click Clear to reset.') }}</span>
                    </div>
                </div>

                <!-- Live Preview Area -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Live KaTeX Preview:') }}</span>
                    <div id="math-dialog-preview" class="min-h-[64px] flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-4 text-xl bg-gray-50/70 overflow-x-auto text-gray-900"></div>
                    <p id="math-dialog-error" class="text-xs text-rose-600 mt-1.5 hidden font-semibold"></p>
                </div>

                <!-- Quick Help / Instructions -->
                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-3 text-xs text-indigo-900 space-y-1">
                    <span class="font-bold flex items-center gap-1 text-indigo-800">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        {{ __('How to use this modal:') }}
                    </span>
                    <ol class="list-decimal list-inside space-y-0.5 text-indigo-800/90 pl-1">
                        <li>{{ __('Click any quick template above or type your LaTeX equation in the box.') }}</li>
                        <li>{{ __('Check the Live KaTeX Preview to verify the formula renders correctly.') }}</li>
                        <li>{{ __('Click "Insert Formula" (or press Ctrl+Enter) to embed it into the editor.') }}</li>
                        <li>{{ __('Click on any existing formula inside the editor anytime to re-open and edit it.') }}</li>
                    </ol>
                </div>
            </div>

            <div class="px-5 py-3.5 border-t border-gray-200 flex justify-end gap-2 bg-slate-50">
                <button type="button" onclick="window.mathFormulaModal.close()" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 transition">{{ __('Cancel') }}</button>
                <button type="button" id="math-dialog-save" onclick="window.mathFormulaModal.save()" class="px-5 py-2 text-sm font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span id="math-dialog-save-text">{{ __('Insert Formula') }}</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        window.qEditor = null;
        window.eEditor = null;
        window.optEditors = {};
        window.lastActiveQuill = null;

        const fullToolbar = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['formula'],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean']
        ];

        const simpleToolbar = [
            ['bold', 'italic', 'underline'],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            ['formula'],
            ['image'],
            ['clean']
        ];

        /* ---------- Math symbol palette ---------- */
        const MATH_SYMBOLS = ['×','÷','±','∓','=','≠','≈','≤','≥','<','>','∞','∝','∴','∵','%','°','√','∛','π','θ','α','β','γ','Δ','Σ','∠','⊥','∥','½','¼','¾','²','³','⁻¹'];
        const MATH_TEMPLATES = [
            { label: 'a⁄b', latex: '\\frac{a}{b}', title: 'a/b (Fraction)' },
            { label: '√x', latex: '\\sqrt{x}', title: '√x (Square Root)' },
            { label: '∛x', latex: '\\sqrt[3]{x}', title: '∛x (Cube Root)' },
            { label: 'xⁿ', latex: 'x^{n}', title: 'xⁿ (Power)' },
            { label: 'xₙ', latex: 'x_{n}', title: 'xₙ (Subscript)' },
            { label: '√(a+b)²', latex: '\\sqrt{(a+b)^{2}}', title: '√(a+b)² (Root of Expression)' },
            { label: '∛24389', latex: '\\sqrt[3]{24389}', title: '∛24389 (Cube Root of Number)' },
            { label: '[(2⁴)¹⁄²]¹', latex: '\\left[\\left(2^{4}\\right)^{1/2}\\right]^{1}', title: '[(2⁴)¹⁄²]¹ (Nested Powers)' },
            { label: '(2²×2³)/(2²×2³)', latex: '\\frac{2^{2} \\times 2^{3}}{2^{2} \\times 2^{3}}', title: '(2²×2³)/(2²×2³) (Fraction with Powers)' },
            { label: '(¹⁄₂₁₆)⁻²⁄³+...', latex: '\\left(\\frac{1}{216}\\right)^{-2/3} + \\left(\\frac{1}{256}\\right)^{-3/4} + \\left(\\frac{1}{243}\\right)^{-1/3}', title: '(1/216)⁻²⁄³ + (1/256)⁻³⁄⁴ (Fraction Powers Sum)' },
            { label: 'x=(-b±√Δ)/2a', latex: 'x = \\frac{-b \\pm \\sqrt{b^{2}-4ac}}{2a}', title: 'x = (-b±√(b²-4ac))/2a (Quadratic Formula)' },
            { label: 'sin²θ+cos²θ=1', latex: '\\sin^{2}\\theta + \\cos^{2}\\theta = 1', title: 'sin²θ + cos²θ = 1 (Trig Identity)' },
            { label: '∑xᵢ', latex: '\\sum_{i=1}^{n} x_{i}', title: '∑ᵢ₌₁ⁿ xᵢ (Summation Series)' },
            { label: 'dy/dx', latex: '\\frac{dy}{dx}', title: 'dy/dx (Derivative)' },
            { label: '∫dx', latex: '\\int f(x) \\, dx', title: '∫ f(x)dx (Integral)' },
        ];

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        /* Lean HTML for storage: keep formula LaTeX in data-value, drop heavy KaTeX markup. */
        function cleanHtml(quill) {
            if (!quill || !quill.root) return '';
            const clone = quill.root.cloneNode(true);
            clone.querySelectorAll('.ql-formula').forEach(el => {
                el.innerHTML = escapeHtml(el.getAttribute('data-value') || '');
            });
            return clone.innerHTML;
        }

        /* ---------- Formula Modal Object (Global) ---------- */
        window.mathFormulaModal = {
            targetQuill: null,
            editIndex: null,
            insertIndex: null,

            el() {
                return document.getElementById('math-formula-dialog');
            },
            input() {
                return document.getElementById('math-dialog-input');
            },
            open(quill, latex = '', editIndex = null) {
                this.targetQuill = quill || window.lastActiveQuill || window.qEditor;
                this.editIndex = editIndex;

                const q = this.targetQuill;
                let targetIdx = 0;
                if (q) {
                    let range = null;
                    try { range = q.getSelection(); } catch (e) {}
                    const maxSafe = Math.max(0, q.getLength() - 1);
                    if (range && typeof range.index === 'number') {
                        targetIdx = Math.min(range.index, maxSafe);
                    } else {
                        targetIdx = maxSafe;
                    }
                }
                this.insertIndex = targetIdx;

                const inp = this.input();
                if (inp) {
                    inp.value = latex;
                }

                const saveText = document.getElementById('math-dialog-save-text');
                if (saveText) {
                    saveText.textContent = (editIndex !== null) ? 'Update Formula' : 'Insert Formula';
                }

                const dialog = this.el();
                if (dialog) {
                    dialog.classList.remove('hidden');
                    dialog.classList.add('flex');
                }

                this.preview();
                setTimeout(() => {
                    if (inp) {
                        inp.focus();
                        inp.setSelectionRange(inp.value.length, inp.value.length);
                    }
                }, 50);
            },
            close() {
                const dialog = this.el();
                if (dialog) {
                    dialog.classList.add('hidden');
                    dialog.classList.remove('flex');
                }
            },
            clear() {
                const inp = this.input();
                if (inp) {
                    inp.value = '';
                    inp.focus();
                }
                const err = document.getElementById('math-dialog-error');
                if (err) {
                    err.classList.add('hidden');
                    err.textContent = '';
                }
                this.preview();
            },
            filterTemplates(cat, btn) {
                document.querySelectorAll('.math-cat-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                    b.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                });
                if (btn) {
                    btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                }
                document.querySelectorAll('#math-dialog-snippets button').forEach(el => {
                    if (cat === 'all' || el.getAttribute('data-category') === cat) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
            },
            insertSnippet(snippet) {
                const inp = this.input();
                if (!inp) return;
                const start = inp.selectionStart || 0;
                const end = inp.selectionEnd || 0;
                inp.value = inp.value.slice(0, start) + snippet + inp.value.slice(end);
                inp.focus();
                const newPos = start + snippet.length;
                inp.setSelectionRange(newPos, newPos);
                this.preview();
            },
            preview() {
                const out = document.getElementById('math-dialog-preview');
                const err = document.getElementById('math-dialog-error');
                const inp = this.input();
                if (!out || !inp) return;

                if (err) err.classList.add('hidden');

                const val = inp.value.trim();
                if (!val) {
                    out.innerHTML = '<span class="text-gray-400 text-sm italic">Type formula above to see live preview...</span>';
                    return;
                }

                try {
                    katex.render(val, out, { throwOnError: true, displayMode: true });
                } catch (e) {
                    if (err) {
                        err.textContent = 'LaTeX Error: ' + e.message;
                        err.classList.remove('hidden');
                    }
                }
            },
            save() {
                const inp = this.input();
                const latex = inp ? inp.value.trim() : '';
                const q = this.targetQuill || window.lastActiveQuill || window.qEditor;

                if (!q) {
                    this.close();
                    return;
                }

                if (!latex) {
                    this.close();
                    return;
                }

                try {
                    q.focus();

                    // If editing an existing formula blot
                    if (this.editIndex !== null && typeof this.editIndex === 'number') {
                        q.deleteText(this.editIndex, 1, 'user');
                        q.insertEmbed(this.editIndex, 'formula', latex, 'user');
                        try {
                            q.setSelection(this.editIndex + 1, 0, 'user');
                        } catch (e) {}
                    } else {
                        // Inserting new formula: ensure index is strictly inside block (0 .. length - 1)
                        let range = null;
                        try { range = q.getSelection(); } catch (e) {}

                        const maxSafe = Math.max(0, q.getLength() - 1);
                        let idx = (typeof this.insertIndex === 'number') ? Math.min(this.insertIndex, maxSafe) : maxSafe;

                        if (range && typeof range.index === 'number') {
                            if (range.length > 0) {
                                q.deleteText(range.index, range.length, 'user');
                            }
                            idx = Math.min(range.index, Math.max(0, q.getLength() - 1));
                        }

                        try {
                            q.insertEmbed(idx, 'formula', latex, 'user');
                            try {
                                q.insertText(idx + 1, ' ', 'user');
                                q.setSelection(idx + 2, 0, 'user');
                            } catch (e) {
                                try { q.setSelection(idx + 1, 0, 'user'); } catch (e2) {}
                            }
                        } catch (embedErr) {
                            console.warn('insertEmbed failed, trying HTML paste fallback:', embedErr);
                            q.clipboard.dangerouslyPasteHTML(idx, '<span class="ql-formula" data-value="' + escapeHtml(latex) + '">' + escapeHtml(latex) + '</span> ');
                        }
                    }

                    // Sync Quill content with Livewire
                    if (window.syncAllQuill) {
                        window.syncAllQuill();
                    }
                } catch (err) {
                    console.error('Failed to insert formula into editor:', err);
                    const errEl = document.getElementById('math-dialog-error');
                    if (errEl) {
                        errEl.textContent = 'Could not insert into editor: ' + err.message;
                        errEl.classList.remove('hidden');
                    }
                    return;
                }

                this.close();
            }
        };

        // Alias for backward compatibility
        const mathDialog = window.mathFormulaModal;

        /* ---------- MathML -> LaTeX (for pasted Word 365 / Google Docs / MathType equations) ---------- */
        const MO_MAP = { '×':'\\times ', '÷':'\\div ', '±':'\\pm ', '∓':'\\mp ', '≠':'\\neq ', '≤':'\\leq ', '≥':'\\geq ', '≈':'\\approx ', '∞':'\\infty ', '·':'\\cdot ', '−':'-', '∠':'\\angle ', '°':'^{\\circ}', 'π':'\\pi ', 'θ':'\\theta ', 'Δ':'\\Delta ', '∑':'\\sum ', '∴':'\\therefore ', '∵':'\\because ' };
        function mathmlToLatex(node) {
            if (!node) { return ''; }
            if (node.nodeType === Node.TEXT_NODE) { return node.textContent.trim(); }
            const tag = (node.localName || node.nodeName).toLowerCase().replace(/^m:/, '');
            const kids = Array.from(node.children);
            const all = () => kids.map(mathmlToLatex).join('');
            const g = i => '{' + mathmlToLatex(kids[i]) + '}';
            switch (tag) {
                case 'math': case 'mrow': case 'mstyle': case 'semantics': case 'mpadded': case 'mphantom': return all();
                case 'annotation': case 'annotation-xml': return '';
                case 'mi': case 'mn': return node.textContent.trim();
                case 'mtext': return '\\text{' + node.textContent + '}';
                case 'mo': { const t = node.textContent.trim(); return MO_MAP[t] !== undefined ? MO_MAP[t] : t; }
                case 'mspace': return '\\ ';
                case 'mfrac': return '\\frac' + g(0) + g(1);
                case 'msqrt': return '\\sqrt{' + all() + '}';
                case 'mroot': return '\\sqrt[' + mathmlToLatex(kids[1]) + ']' + g(0);
                case 'msup': return g(0) + '^' + g(1);
                case 'msub': return g(0) + '_' + g(1);
                case 'msubsup': return g(0) + '_' + g(1) + '^' + g(2);
                case 'mover': {
                    const acc = (kids[1] && kids[1].textContent.trim()) || '';
                    if (acc === '¯' || acc === '‾' || acc === '_') { return '\\overline' + g(0); }
                    return '\\overset' + g(1) + g(0);
                }
                case 'munder': return '\\underset' + g(1) + g(0);
                case 'munderover': return g(0) + '_' + g(1) + '^' + g(2);
                case 'mfenced': {
                    const open = node.getAttribute('open') ?? '(', close = node.getAttribute('close') ?? ')';
                    return '\\left' + (open || '.') + kids.map(mathmlToLatex).join(',') + '\\right' + (close || '.');
                }
                case 'mtable': return '\\begin{matrix}' + kids.map(mathmlToLatex).join('\\\\') + '\\end{matrix}';
                case 'mtr': return kids.map(mathmlToLatex).join('&');
                case 'mtd': return all();
                default: return all();
            }
        }

        /* Split plain text containing $..$, \(..\), \[..\] into text + formula ops. */
        const DELIM_RE = /\$\$([\s\S]+?)\$\$|\\\[([\s\S]+?)\\\]|\\\(([\s\S]+?)\\\)|\$([^$\n]+?)\$/g;
        function textToDelta(text, Delta) {
            const d = new Delta();
            let last = 0, m, found = false;
            DELIM_RE.lastIndex = 0;
            while ((m = DELIM_RE.exec(text)) !== null) {
                found = true;
                if (m.index > last) { d.insert(text.slice(last, m.index)); }
                d.insert({ formula: (m[1] || m[2] || m[3] || m[4]).trim() });
                last = DELIM_RE.lastIndex;
            }
            if (!found) { return null; }
            if (last < text.length) { d.insert(text.slice(last)); }
            return d;
        }

        function attachMathTools(quill, hostEl) {
            const Delta = Quill.import('delta');

            // Formula toolbar button -> our modal
            quill.getModule('toolbar').addHandler('formula', () => window.mathFormulaModal.open(quill));

            // Track last active quill on focus and selection
            quill.root.addEventListener('focus', () => { window.lastActiveQuill = quill; });
            quill.on('selection-change', (r) => { if (r) window.lastActiveQuill = quill; });

            // Paste: MathML elements become formulas
            quill.clipboard.addMatcher('math', (node) => {
                const latex = mathmlToLatex(node).trim();
                return latex ? new Delta().insert({ formula: latex }) : new Delta();
            });
            // Paste: KaTeX-rendered HTML (copied from our own pages / web) -> keep LaTeX source
            quill.clipboard.addMatcher('.katex', (node) => {
                const ann = node.querySelector('annotation[encoding="application/x-tex"]');
                return ann ? new Delta().insert({ formula: ann.textContent.trim() }) : new Delta().insert(node.textContent);
            });
            // Paste: LaTeX delimiters inside plain text -> formulas
            quill.clipboard.addMatcher(Node.TEXT_NODE, (node, delta) => {
                const converted = textToDelta(node.data, Delta);
                return converted || delta;
            });

            // Click an existing formula to edit it
            quill.root.addEventListener('click', (ev) => {
                const f = ev.target.closest('.ql-formula');
                if (!f || !quill.root.contains(f)) { return; }
                const blot = Quill.find(f);
                if (!blot) { return; }
                const idx = quill.getIndex(blot);
                window.mathFormulaModal.open(quill, f.getAttribute('data-value') || '', idx);
            });

            // Symbol palette above the toolbar
            const toolbarEl = quill.getModule('toolbar').container;
            if (toolbarEl && !toolbarEl.previousElementSibling?.classList.contains('math-palette')) {
                const pal = document.createElement('div');
                pal.className = 'math-palette';
                MATH_SYMBOLS.forEach(sym => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.textContent = sym;
                    b.title = 'Insert ' + sym;
                    b.addEventListener('mousedown', e => e.preventDefault());
                    b.addEventListener('click', () => {
                        quill.focus();
                        let r = null;
                        try { r = quill.getSelection(); } catch(e){}
                        const maxSafe = Math.max(0, quill.getLength() - 1);
                        let i = (r && typeof r.index === 'number') ? Math.min(r.index, maxSafe) : maxSafe;
                        if (r && r.length > 0) {
                            quill.deleteText(r.index, r.length, 'user');
                            i = Math.min(r.index, Math.max(0, quill.getLength() - 1));
                        }
                        quill.insertText(i, sym, 'user');
                        try { quill.setSelection(i + sym.length, 0, 'user'); } catch(e){}
                    });
                    pal.appendChild(b);
                });
                const sep = document.createElement('span');
                sep.className = 'math-palette-sep';
                pal.appendChild(sep);
                MATH_TEMPLATES.forEach(t => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.textContent = t.label;
                    b.title = t.title + ' (formula)';
                    b.className = 'math-tpl';
                    b.addEventListener('mousedown', e => e.preventDefault());
                    b.addEventListener('click', () => window.mathFormulaModal.open(quill, t.latex));
                    pal.appendChild(b);
                });
                toolbarEl.parentNode.insertBefore(pal, toolbarEl);
            }
        }

        function removeStrayChrome(el) {
            while (el.previousElementSibling && (el.previousElementSibling.classList.contains('ql-toolbar') || el.previousElementSibling.classList.contains('math-palette'))) {
                el.previousElementSibling.remove();
            }
        }

        function buildEditor(el, toolbar, onChange) {
            removeStrayChrome(el);
            const quill = new Quill(el, { theme: 'snow', modules: { formula: true, toolbar: toolbar } });
            attachMathTools(quill, el);
            quill.root.addEventListener('focus', () => { window.lastActiveQuill = quill; });
            quill.on('selection-change', (r) => { if (r) window.lastActiveQuill = quill; });
            quill.on('text-change', () => onChange(cleanHtml(quill)));
            return quill;
        }

        function initQuill() {
            if (typeof Quill === 'undefined' || typeof katex === 'undefined') {
                setTimeout(initQuill, 100);
                return;
            }
            window.katex = katex;

            // 1. Question Editor
            const qEl = document.getElementById('question-editor');
            if (qEl && !qEl.classList.contains('ql-container')) {
                window.qEditor = buildEditor(qEl, fullToolbar, html => @this.set('question_html', html, false));
                if (!window.lastActiveQuill) {
                    window.lastActiveQuill = window.qEditor;
                }
            }

            // 2. Explanation Editor
            const eEl = document.getElementById('explanation-editor');
            if (eEl && !eEl.classList.contains('ql-container')) {
                window.eEditor = buildEditor(eEl, fullToolbar, html => @this.set('explanation_html', html, false));
            }

            // 3. Option Editors (each independent)
            document.querySelectorAll('.option-editor').forEach(el => {
                if (!el.classList.contains('ql-container')) {
                    const idx = el.getAttribute('data-index');
                    window.optEditors[idx] = buildEditor(el, simpleToolbar, html => @this.set('options.' + idx + '.option_html', html, false));
                }
            });
        }

        window.syncAllQuill = function() {
            if (window.qEditor) {
                @this.set('question_html', cleanHtml(window.qEditor), false);
            }
            if (window.eEditor) {
                @this.set('explanation_html', cleanHtml(window.eEditor), false);
            }
            document.querySelectorAll('.option-editor').forEach(el => {
                const idx = el.getAttribute('data-index');
                if (window.optEditors && window.optEditors[idx]) {
                    @this.set('options.' + idx + '.option_html', cleanHtml(window.optEditors[idx]), false);
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
