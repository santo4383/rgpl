<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">ParikshaMitra at a Glance (Statistics)</h1>
                <p class="text-sm text-gray-500 mt-1">Configure metrics displayed on the homepage. You can tie them to dynamic DB counters or set manual custom figures.</p>
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Metric
            </button>
        </div>

        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                {{ session('message') }}
            </div>
        @endif

        <!-- Stats Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Order</th>
                            <th class="px-6 py-3.5 text-left">Title</th>
                            <th class="px-6 py-3.5 text-left">Displayed Value</th>
                            <th class="px-6 py-3.5 text-left">Calculation Mode</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($statistics as $stat)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $stat->display_order }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $stat->title }}</div>
                                    @if($stat->description)
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $stat->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-base font-black text-amber-600">{{ $stat->display_value }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if(!empty($stat->value))
                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-semibold">Manual Override</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold">Dynamic ({{ $stat->stat_key ?? 'Auto' }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleStatus({{ $stat->id }})" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold transition {{ $stat->status === 'ACTIVE' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $stat->status }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button wire:click="edit({{ $stat->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $stat->id }})" wire:confirm="Delete this metric?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No statistics configured. Click "Add Metric" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
                <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $statId ? 'Edit Metric' : 'Add Metric' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4 text-sm">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Metric Title *</label>
                            <input type="text" wire:model="title" placeholder="e.g. Total Questions" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Manual Value Override</label>
                                <input type="text" wire:model="value" placeholder="e.g. 10,000+" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                                <span class="text-[10px] text-gray-500">Leave blank to use dynamic calculation</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Dynamic Counter Key</label>
                                <select wire:model="stat_key" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs">
                                    <option value="">-- None / Manual Only --</option>
                                    <option value="total_questions">total_questions (DB Count)</option>
                                    <option value="total_students">total_students (DB Count)</option>
                                    <option value="free_questions">free_questions (Config Quota)</option>
                                    <option value="total_courses">total_courses (Active Fields)</option>
                                    <option value="mock_exams">mock_exams (Exam Bank)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Description / Subtext</label>
                            <input type="text" wire:model="description" placeholder="e.g. Curated MCQs with bilingual explanations" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Display Order</label>
                                <input type="number" wire:model="display_order" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Metric
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
