<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">FAQ Management</h1>
                <p class="text-sm text-gray-500 mt-1">Add, categorize, and organize questions and answers shown on the public website and FAQ page.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New FAQ
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search questions or answers..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="categoryFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Order</th>
                            <th class="px-6 py-3.5 text-left">Question &amp; Category</th>
                            <th class="px-6 py-3.5 text-left">Homepage Featured</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($faqs as $f)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $f->display_order }}
                                </td>
                                <td class="px-6 py-4 max-w-lg">
                                    <div class="font-bold text-gray-900">{{ $f->question }}</div>
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ strip_tags($f->answer_html) }}
                                    </div>
                                    <span class="inline-flex mt-2 px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[10px] uppercase">
                                        {{ $f->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleHomepageFeatured({{ $f->id }})" class="text-xs font-bold transition {{ $f->is_featured_homepage ? 'text-amber-600 hover:text-amber-700' : 'text-gray-400 hover:text-gray-600' }}">
                                        {{ $f->is_featured_homepage ? '★ Homepage' : '☆ All FAQs only' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleStatus({{ $f->id }})" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold transition {{ $f->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $f->status }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button wire:click="edit({{ $f->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $f->id }})" wire:confirm="Are you sure you want to delete this FAQ?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No FAQs found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($faqs->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $faqId ? 'Edit FAQ' : 'Add FAQ' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <!-- Question -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Question *</label>
                            <input type="text" wire:model="question" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @error('question') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Answer HTML -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Answer HTML *</label>
                            <textarea wire:model="answer_html" rows="5" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('answer_html') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category & Display Order & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Category *</label>
                                <input type="text" wire:model="category" placeholder="e.g. GENERAL, EXAMS, PRICING" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                                @error('category') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Display Order</label>
                                <input type="number" wire:model="display_order" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="ACTIVE">Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Featured Checkbox -->
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" wire:model="is_featured_homepage" id="faq_featured" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="faq_featured" class="text-xs font-bold text-gray-700">Display this item on the homepage FAQ accordion</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save FAQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
