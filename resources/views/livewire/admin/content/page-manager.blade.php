<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Static Pages &amp; Legal Policies</h1>
                <p class="text-sm text-gray-500 mt-1">Manage Privacy Policy (21 sections), Terms &amp; Conditions (23 sections), Refund Policy (10 sections), and custom pages.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Page
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search page title or slug..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Page Title</th>
                            <th class="px-6 py-3.5 text-left">URL Slug</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Last Modified</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pages as $p)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $p->title }}</div>
                                    @if($p->meta_title)
                                        <div class="text-xs text-gray-400 mt-0.5">SEO: {{ $p->meta_title }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-indigo-600">
                                    /{{ $p->slug }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $p->status === 'PUBLISHED' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $p->updated_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="/{{ $p->slug }}" target="_blank" class="font-bold text-xs text-gray-600 hover:text-gray-900">
                                        View
                                    </a>
                                    <button wire:click="edit({{ $p->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    @if(!in_array($p->slug, ['privacy-policy', 'terms-and-conditions', 'refund-policy', 'about-us']))
                                        <button wire:click="delete({{ $p->id }})" wire:confirm="Delete this page?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No pages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pages->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-4xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $pageId ? 'Edit Page Content' : 'Create New Page' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Page Title *</label>
                                <input type="text" wire:model="title" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                                @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Slug (URL path) *</label>
                                <input type="text" wire:model="slug" required placeholder="e.g. privacy-policy" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 font-mono text-xs">
                                @error('slug') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Content HTML *</label>
                            <textarea wire:model="content_html" rows="12" required class="w-full font-mono text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('content_html') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-gray-100">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Meta Title (SEO)</label>
                                <input type="text" wire:model="meta_title" class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-semibold">
                                    <option value="PUBLISHED">Published</option>
                                    <option value="DRAFT">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Meta Description (SEO)</label>
                            <input type="text" wire:model="meta_description" class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Page
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
