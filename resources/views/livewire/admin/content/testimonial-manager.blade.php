<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Student Reviews &amp; Testimonials</h1>
                <p class="text-sm text-gray-500 mt-1">Manage feedback, student ratings, and success stories displayed on the homepage.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Testimonial
            </button>
        </div>

        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search student name or exam..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700">
                    <option value="">All Statuses</option>
                    <option value="PUBLISHED">Published</option>
                    <option value="APPROVED">Approved</option>
                    <option value="DRAFT">Draft</option>
                    <option value="ARCHIVED">Archived</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Aspirant / Student</th>
                            <th class="px-6 py-3.5 text-left">Exam Stream</th>
                            <th class="px-6 py-3.5 text-left">Rating &amp; Review</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Featured</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($testimonials as $t)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">
                                            {{ substr($t->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $t->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $t->designation ?? 'Aspirant' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $t->field ?? 'General' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <div class="flex items-center text-amber-400 text-xs mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $t->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                    </div>
                                    <div class="text-xs text-gray-600 line-clamp-2 italic">
                                        "{{ $t->content }}"
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'PUBLISHED' => 'bg-emerald-100 text-emerald-800',
                                            'APPROVED' => 'bg-blue-100 text-blue-800',
                                            'DRAFT' => 'bg-gray-100 text-gray-600',
                                            'ARCHIVED' => 'bg-slate-200 text-slate-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $colors[$t->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleFeatured({{ $t->id }})" class="text-xs font-bold transition {{ $t->featured ? 'text-amber-600 hover:text-amber-700' : 'text-gray-400 hover:text-gray-600' }}">
                                        {{ $t->featured ? '★ Featured' : '☆ Standard' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button wire:click="edit({{ $t->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this testimonial?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No testimonials found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($testimonials->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $testimonials->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $testimonialId ? 'Edit Testimonial' : 'Add Testimonial' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Student / Aspirant Name *</label>
                                <input type="text" wire:model="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                                @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Designation / Rank / Score</label>
                                <input type="text" wire:model="designation" placeholder="e.g. Cleared PSI Exam 2025" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Stream / Field</label>
                                <input type="text" wire:model="field" placeholder="e.g. MPSC, Police Bharti, Commerce" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Star Rating (1-5) *</label>
                                <select wire:model="rating" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="5">5 Stars - Excellent</option>
                                    <option value="4">4 Stars - Very Good</option>
                                    <option value="3">3 Stars - Good</option>
                                    <option value="2">2 Stars - Fair</option>
                                    <option value="1">1 Star - Poor</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Review Content *</label>
                            <textarea wire:model="content" rows="4" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('content') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status *</label>
                                <select wire:model="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="PUBLISHED">Published</option>
                                    <option value="APPROVED">Approved</option>
                                    <option value="DRAFT">Draft</option>
                                    <option value="ARCHIVED">Archived</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Display Order</label>
                                <input type="number" wire:model="display_order" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" wire:model="featured" id="testimonial_featured" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="testimonial_featured" class="text-xs font-bold text-gray-700">Display on Homepage Testimonials Carousel</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Testimonial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
