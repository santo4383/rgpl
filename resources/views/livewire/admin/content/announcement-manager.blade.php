<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Announcements &amp; Exam Alerts</h1>
                <p class="text-sm text-gray-500 mt-1">Manage ticker announcements, alerts, and notifications displayed to visitors and students.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Announcement
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search announcements..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700">
                    <option value="">All Statuses</option>
                    <option value="DRAFT">Draft</option>
                    <option value="REVIEW">Under Review</option>
                    <option value="PUBLISHED">Published</option>
                    <option value="SCHEDULED">Scheduled</option>
                    <option value="EXPIRED">Expired</option>
                    <option value="ARCHIVED">Archived</option>
                </select>

                <select wire:model.live="priorityFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700">
                    <option value="">All Priorities</option>
                    <option value="URGENT">Urgent</option>
                    <option value="HIGH">High</option>
                    <option value="MEDIUM">Medium</option>
                    <option value="LOW">Low</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Title &amp; Category</th>
                            <th class="px-6 py-3.5 text-left">Priority</th>
                            <th class="px-6 py-3.5 text-left">Validity Schedule</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Featured</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($announcements as $a)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $a->title }}</div>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-100 font-medium text-[10px] text-gray-700 uppercase">{{ $a->category ?? 'GENERAL' }}</span>
                                        @if($a->link)
                                            <a href="{{ $a->link }}" target="_blank" class="text-indigo-600 hover:underline inline-flex items-center text-[11px]">
                                                <span>{{ $a->button_text ?? 'Link' }}</span>
                                                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                    @if($a->short_description)
                                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $a->short_description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityStyles = [
                                            'URGENT' => 'bg-red-100 text-red-800 border-red-300',
                                            'HIGH' => 'bg-amber-100 text-amber-900 border-amber-300',
                                            'MEDIUM' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'LOW' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $priorityStyles[$a->priority] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                        {{ $a->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if($a->start_date || $a->end_date)
                                        <div>{{ $a->start_date?->format('M d, Y') ?? 'Immediate' }} &ndash; {{ $a->end_date?->format('M d, Y') ?? 'Never' }}</div>
                                    @else
                                        <span class="text-gray-400">Always Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        @php
                                            $statusColors = [
                                                'PUBLISHED' => 'bg-emerald-100 text-emerald-800',
                                                'DRAFT' => 'bg-gray-100 text-gray-700',
                                                'REVIEW' => 'bg-amber-100 text-amber-800',
                                                'SCHEDULED' => 'bg-blue-100 text-blue-800',
                                                'EXPIRED' => 'bg-rose-100 text-rose-800',
                                                'ARCHIVED' => 'bg-slate-200 text-slate-700',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold {{ $statusColors[$a->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $a->status }}
                                        </span>
                                    </div>
                                    <!-- Workflow Quick Action -->
                                    <div class="mt-1 flex items-center gap-1 text-[11px]">
                                        @if($a->status !== 'PUBLISHED')
                                            <button wire:click="updateStatus({{ $a->id }}, 'PUBLISHED')" class="text-emerald-700 hover:underline font-semibold">Publish</button>
                                        @endif
                                        @if($a->status !== 'ARCHIVED')
                                            <span class="text-gray-300">&bull;</span>
                                            <button wire:click="updateStatus({{ $a->id }}, 'ARCHIVED')" class="text-slate-600 hover:underline font-semibold">Archive</button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($a->featured)
                                        <span class="inline-flex items-center text-xs font-bold text-amber-600">
                                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            Featured
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">&ndash;</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button wire:click="edit({{ $a->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $a->id }})" wire:confirm="Are you sure you want to delete this announcement?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No announcements found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $announcementId ? 'Edit Announcement' : 'Create Announcement' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Title *</label>
                            <input type="text" wire:model="title" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Short Ticker Text / Description</label>
                            <input type="text" wire:model="short_description" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @error('short_description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Full Content -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Full Detailed Content (Optional)</label>
                            <textarea wire:model="content" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('content') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Priority & Category & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Priority *</label>
                                <select wire:model="priority" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="URGENT">Urgent (Red Alert)</option>
                                    <option value="HIGH">High</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="LOW">Low</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Category</label>
                                <input type="text" wire:model="category" placeholder="e.g. EXAM_ALERT, ADMIT_CARD" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Workflow Status *</label>
                                <select wire:model="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300">
                                    <option value="DRAFT">Draft</option>
                                    <option value="REVIEW">Under Review</option>
                                    <option value="PUBLISHED">Published</option>
                                    <option value="SCHEDULED">Scheduled</option>
                                    <option value="EXPIRED">Expired</option>
                                    <option value="ARCHIVED">Archived</option>
                                </select>
                            </div>
                        </div>

                        <!-- Link & Button Text -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Target URL / Link</label>
                                <input type="text" wire:model="link" placeholder="e.g. /courses or https://..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Button Text</label>
                                <input type="text" wire:model="button_text" placeholder="e.g. Learn More" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                        </div>

                        <!-- Schedule Dates -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Publish Start Date</label>
                                <input type="datetime-local" wire:model="start_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Publish End Date</label>
                                <input type="datetime-local" wire:model="end_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                        </div>

                        <!-- Featured Checkbox -->
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" wire:model="featured" id="announcement_featured" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="announcement_featured" class="text-xs font-bold text-gray-700">Display as Featured announcement</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
