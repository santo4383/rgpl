<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Homepage Hero Sliders</h1>
                <p class="text-sm text-gray-500 mt-1">Manage promotional banners, titles, and CTA buttons on the public homepage slider.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Slide
            </button>
        </div>

        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- Sliders Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Order</th>
                            <th class="px-6 py-3.5 text-left">Title &amp; Subtitle</th>
                            <th class="px-6 py-3.5 text-left">CTA Button</th>
                            <th class="px-6 py-3.5 text-left">Schedule</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sliders as $slider)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $slider->display_order }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $slider->title }}</div>
                                    @if($slider->subtitle)
                                        <div class="text-xs text-indigo-600 mt-0.5">{{ $slider->subtitle }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @if($slider->button_text)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-100 font-semibold">{{ $slider->button_text }} &rarr; {{ $slider->button_url }}</span>
                                    @else
                                        <span class="text-gray-400">No CTA</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if($slider->start_date || $slider->end_date)
                                        <div>{{ $slider->start_date?->format('M d, Y') ?? 'Anytime' }} &ndash; {{ $slider->end_date?->format('M d, Y') ?? 'Indefinite' }}</div>
                                    @else
                                        <span>Always Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleActive({{ $slider->id }})" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold transition {{ $slider->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $slider->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button wire:click="edit({{ $slider->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $slider->id }})" wire:confirm="Are you sure you want to delete this slider?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No homepage sliders configured yet. Click "Add New Slide" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $sliderId ? 'Edit Hero Slide' : 'Create New Hero Slide' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Slide Title *</label>
                            <input type="text" wire:model="title" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Subtitle / Badge</label>
                            <input type="text" wire:model="subtitle" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @error('subtitle') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Short Description</label>
                            <textarea wire:model="description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Buttons Row 1 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Primary Button Text</label>
                                <input type="text" wire:model="button_text" placeholder="e.g. Start Practicing" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Primary Button URL</label>
                                <input type="text" wire:model="button_url" placeholder="e.g. /practice" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                        </div>

                        <!-- Buttons Row 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Secondary Button Text</label>
                                <input type="text" wire:model="secondary_button_text" placeholder="e.g. Explore Courses" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Secondary Button URL</label>
                                <input type="text" wire:model="secondary_button_url" placeholder="e.g. /courses" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Desktop Image (Optional)</label>
                                <input type="file" wire:model="desktop_image_upload" class="text-xs text-gray-500">
                                @if($existing_desktop_image)
                                    <div class="text-[11px] text-gray-500 mt-1">Current: {{ $existing_desktop_image }}</div>
                                @endif
                                @error('desktop_image_upload') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Mobile Image (Optional)</label>
                                <input type="file" wire:model="mobile_image_upload" class="text-xs text-gray-500">
                                @if($existing_mobile_image)
                                    <div class="text-[11px] text-gray-500 mt-1">Current: {{ $existing_mobile_image }}</div>
                                @endif
                                @error('mobile_image_upload') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Scheduling & Order -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Display Order</label>
                                <input type="number" wire:model="display_order" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Start Date</label>
                                <input type="datetime-local" wire:model="start_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">End Date</label>
                                <input type="datetime-local" wire:model="end_date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs">
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" wire:model="is_active" id="slider_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="slider_active" class="text-xs font-bold text-gray-700">Slide is Active and visible on homepage</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Slide
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
