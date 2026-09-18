<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">News, Articles &amp; Syllabus Updates</h1>
                <p class="text-sm text-gray-500 mt-1">Publish editorial guides, examination syllabus notifications, and academic tips.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Write New Article
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title, author, topic..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700" {{ $showTrashed ? 'disabled' : '' }}>
                    <option value="">All Statuses</option>
                    <option value="PUBLISHED">Published</option>
                    <option value="DRAFT">Draft</option>
                    <option value="REVIEW">Review</option>
                    <option value="SCHEDULED">Scheduled</option>
                    <option value="ARCHIVED">Archived</option>
                </select>

                <button wire:click="$toggle('showTrashed')" class="text-xs px-3 py-2 rounded-xl border font-bold transition {{ $showTrashed ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    {{ $showTrashed ? 'Viewing Trash' : 'View Trash' }}
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Article Title &amp; Slug</th>
                            <th class="px-6 py-3.5 text-left">Category / Author</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Featured</th>
                            <th class="px-6 py-3.5 text-left">Publish Date</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($articles as $a)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $a->title }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono mt-0.5">/news/{{ $a->slug }}</div>
                                    @if($a->short_description)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $a->short_description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[10px] uppercase">
                                        {{ $a->category ?? 'General' }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">By {{ $a->author ?? 'Editorial' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'PUBLISHED' => 'bg-emerald-100 text-emerald-800',
                                            'DRAFT' => 'bg-gray-100 text-gray-700',
                                            'REVIEW' => 'bg-amber-100 text-amber-800',
                                            'SCHEDULED' => 'bg-blue-100 text-blue-800',
                                            'ARCHIVED' => 'bg-slate-200 text-slate-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$a->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $a->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleFeatured({{ $a->id }})" class="text-xs font-bold transition {{ $a->featured ? 'text-amber-600 hover:text-amber-700' : 'text-gray-400 hover:text-gray-600' }}">
                                        {{ $a->featured ? '★ Featured' : '☆ Standard' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $a->publish_date?->format('M d, Y') ?? 'Draft' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if($showTrashed)
                                        <button wire:click="restore({{ $a->id }})" class="font-bold text-xs text-emerald-600 hover:text-emerald-800">
                                            Restore
                                        </button>
                                        <button wire:click="forceDelete({{ $a->id }})" wire:confirm="Permanently delete this article? This cannot be undone." class="font-bold text-xs text-red-600 hover:text-red-800">
                                            Permanent Delete
                                        </button>
                                    @else
                                        <a href="{{ route('public.news.show', $a->slug) }}" target="_blank" class="font-bold text-xs text-gray-500 hover:text-gray-700">
                                            Preview
                                        </a>
                                        <button wire:click="edit({{ $a->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $a->id }})" wire:confirm="Move this article to trash?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                            Trash
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No articles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($articles->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-3xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $articleId ? 'Edit Article' : 'Compose News Article' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <!-- Title & Slug -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Article Title *</label>
                                <input type="text" wire:model="title" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                                @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Custom Slug (Leave blank to auto-generate)</label>
                                <input type="text" wire:model="slug" placeholder="e.g. mpsc-2026-notification" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 font-mono text-xs">
                                @error('slug') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Category & Author & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Category</label>
                                <input type="text" wire:model="category" placeholder="e.g. MPSC, Syllabus, Exam Alert" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Author Name</label>
                                <input type="text" wire:model="author" placeholder="e.g. ParikshaMitra Editorial" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status *</label>
                                <select wire:model="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="PUBLISHED">Published</option>
                                    <option value="DRAFT">Draft</option>
                                    <option value="REVIEW">Review</option>
                                    <option value="SCHEDULED">Scheduled</option>
                                    <option value="ARCHIVED">Archived</option>
                                </select>
                            </div>
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Summary / Excerpt</label>
                            <textarea wire:model="short_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300"></textarea>
                            @error('short_description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Full Content HTML -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Article Body (HTML supported) *</label>
                            <textarea wire:model="content_html" rows="8" required class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('content_html') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- SEO Metadata -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Meta Title (SEO)</label>
                                <input type="text" wire:model="meta_title" placeholder="Title for Google search" class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Display Order</label>
                                <input type="number" wire:model="display_order" min="0" class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Meta Description (SEO)</label>
                            <input type="text" wire:model="meta_description" placeholder="Short description for Google snippet" class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs">
                        </div>

                        <!-- Dates & Featured -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Publish Date</label>
                                <input type="datetime-local" wire:model="publish_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Expiry Date (Optional)</label>
                                <input type="datetime-local" wire:model="expiry_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" wire:model="featured" id="news_featured" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="news_featured" class="text-xs font-bold text-gray-700">Feature this article on homepage and top of news list</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
