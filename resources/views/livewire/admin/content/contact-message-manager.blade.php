<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Contact Inquiries &amp; Helpdesk</h1>
                <p class="text-sm text-gray-500 mt-1">Review student inquiries, add admin follow-up notes, assign handlers, and filter spam.</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search sender, email, mobile, subject..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="text-xs px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-700">
                    <option value="">Active Inquiries (Excl. Spam)</option>
                    <option value="NEW">New Inquiries</option>
                    <option value="IN_PROGRESS">In Progress</option>
                    <option value="RESOLVED">Resolved</option>
                    <option value="CLOSED">Closed</option>
                    <option value="SPAM">Spam Quarantine</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Sender Info</th>
                            <th class="px-6 py-3.5 text-left">Subject &amp; Excerpt</th>
                            <th class="px-6 py-3.5 text-left">Assigned Staff</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Date</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($messages as $m)
                            <tr class="hover:bg-gray-50/80 transition {{ $m->status === 'NEW' ? 'bg-amber-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 flex items-center gap-2">
                                        {{ $m->name }}
                                        @if($m->status === 'NEW')
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">NEW</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <a href="mailto:{{ $m->email }}" class="text-indigo-600 hover:underline">{{ $m->email }}</a>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">
                                        {{ $m->mobile }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-sm">
                                    <div class="font-bold text-gray-900 text-xs">{{ $m->subject }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-2 mt-0.5">
                                        {{ $m->message }}
                                    </div>
                                    @if($m->admin_notes)
                                        <div class="mt-1 text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            <strong>Note:</strong> {{ $m->admin_notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $m->assignedTo?->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'NEW' => 'bg-amber-100 text-amber-800',
                                            'IN_PROGRESS' => 'bg-blue-100 text-blue-800',
                                            'RESOLVED' => 'bg-emerald-100 text-emerald-800',
                                            'CLOSED' => 'bg-gray-100 text-gray-700',
                                            'SPAM' => 'bg-rose-100 text-rose-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $statusStyles[$m->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $m->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $m->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button wire:click="viewMessage({{ $m->id }})" class="font-bold text-xs text-indigo-600 hover:text-indigo-800">
                                        Details &amp; Reply
                                    </button>
                                    @if($m->status !== 'SPAM')
                                        <button wire:click="markAsSpam({{ $m->id }})" class="font-bold text-xs text-amber-600 hover:text-amber-800">
                                            Spam
                                        </button>
                                    @endif
                                    <button wire:click="delete({{ $m->id }})" wire:confirm="Delete this message?" class="font-bold text-xs text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No contact inquiries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>

        <!-- Detail / Edit Modal -->
        @if($showModal && $selectedMessage)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl my-8">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Inquiry #{{ $selectedMessage->id }}</h2>
                            <p class="text-xs text-gray-500">Submitted on {{ $selectedMessage->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Message Body & Sender Info -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-xl text-xs">
                            <div>
                                <span class="text-gray-400 block uppercase font-bold text-[10px]">Name</span>
                                <span class="font-bold text-gray-800">{{ $selectedMessage->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block uppercase font-bold text-[10px]">Email</span>
                                <a href="mailto:{{ $selectedMessage->email }}" class="text-indigo-600 font-semibold hover:underline">{{ $selectedMessage->email }}</a>
                            </div>
                            <div>
                                <span class="text-gray-400 block uppercase font-bold text-[10px]">Mobile</span>
                                <a href="tel:{{ $selectedMessage->mobile }}" class="text-gray-800 font-semibold font-mono">{{ $selectedMessage->mobile }}</a>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Subject</h3>
                            <div class="p-3 bg-gray-50 rounded-xl text-sm font-semibold text-gray-900 border border-gray-100">
                                {{ $selectedMessage->subject }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Message Content</h3>
                            <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-800 border border-gray-100 whitespace-pre-wrap leading-relaxed">
                                {{ $selectedMessage->message }}
                            </div>
                        </div>

                        @if($selectedMessage->ip_address)
                            <div class="text-[11px] text-gray-400 font-mono">
                                IP: {{ $selectedMessage->ip_address }} | Agent: {{ Str::limit($selectedMessage->user_agent, 60) }}
                            </div>
                        @endif
                    </div>

                    <!-- Admin Resolution Form -->
                    <form wire:submit.prevent="updateMessage" class="space-y-4 pt-4 border-t border-gray-100 text-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Ticket Status</label>
                                <select wire:model="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 font-semibold">
                                    <option value="NEW">New</option>
                                    <option value="IN_PROGRESS">In Progress</option>
                                    <option value="RESOLVED">Resolved</option>
                                    <option value="CLOSED">Closed</option>
                                    <option value="SPAM">Spam</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Assign To Staff</label>
                                <select wire:model="assigned_to" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Internal Admin Follow-up Notes</label>
                            <textarea wire:model="admin_notes" rows="3" placeholder="Add resolution notes, student phone call summary, etc..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 text-xs"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50">
                                Close
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                                Save Notes &amp; Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
