<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Audit Logs & Security Stream') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('Track administrative and student actions, state changes, payment authorizations, and security events.') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                {{ __('Auditing Active') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls -->
            <div class="bg-white p-4 rounded-xl shadow-xs border border-gray-200 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="w-full md:w-1/3">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="{{ __('Search user, mobile, action, record ID, IP...') }}" 
                        class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select wire:model.live="moduleFilter" class="text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('All Modules') }}</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}">{{ $mod }}</option>
                        @endforeach
                    </select>

                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="actionFilter" 
                        placeholder="{{ __('Filter action (e.g. APPROVED)...') }}" 
                        class="text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="bg-white shadow-xs rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('Timestamp') }}</th>
                            <th class="px-6 py-3.5">{{ __('User / Actor') }}</th>
                            <th class="px-6 py-3.5">{{ __('Module') }}</th>
                            <th class="px-6 py-3.5">{{ __('Action & Record') }}</th>
                            <th class="px-6 py-3.5">{{ __('IP Address') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Details') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            @php
                                $badgeColor = match($log->module) {
                                    'AUTH' => 'bg-blue-100 text-blue-800',
                                    'PAYMENTS' => 'bg-emerald-100 text-emerald-800',
                                    'CONTENT' => 'bg-indigo-100 text-indigo-800',
                                    'EXAMS' => 'bg-purple-100 text-purple-800',
                                    'SETTINGS' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="font-bold text-gray-900 text-xs">{{ $log->user->name }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $log->user->mobile_number }}</div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">{{ __('System / Anonymous') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badgeColor }}">
                                        {{ $log->module }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-800">{{ $log->action }}</div>
                                    @if($log->record_id)
                                        <div class="text-[11px] text-gray-500 font-mono">ID: {{ $log->record_id }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if(!empty($log->old_values) || !empty($log->new_values))
                                        <button 
                                            wire:click="viewDetails({{ $log->id }})" 
                                            class="px-2.5 py-1 text-xs font-bold text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                        >
                                            {{ __('View Diff') }} &rarr;
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <p class="font-bold text-sm text-gray-600">{{ __('No audit log records found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>

        </div>
    </div>

    <!-- DIFF INSPECTOR MODAL -->
    @if($showDetailsModal && $selectedLog)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 sm:p-7 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex justify-between items-start border-b pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                {{ $selectedLog->module }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900">{{ $selectedLog->action }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $selectedLog->created_at->format('M d, Y H:i:s') }} &bull; {{ $selectedLog->user ? $selectedLog->user->name : 'System' }} ({{ $selectedLog->ip_address }})
                        </p>
                    </div>
                    <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    @if(!empty($selectedLog->old_values))
                        <div>
                            <span class="font-bold text-rose-700 uppercase tracking-wider block mb-1">{{ __('Old / Previous State:') }}</span>
                            <pre class="bg-gray-50 border border-gray-200 p-3 rounded-xl overflow-x-auto text-[11px] font-mono text-gray-800">{{ json_encode($selectedLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    @if(!empty($selectedLog->new_values))
                        <div>
                            <span class="font-bold text-emerald-700 uppercase tracking-wider block mb-1">{{ __('New / Updated State:') }}</span>
                            <pre class="bg-gray-50 border border-gray-200 p-3 rounded-xl overflow-x-auto text-[11px] font-mono text-gray-800">{{ json_encode($selectedLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    @if($selectedLog->user_agent)
                        <div class="pt-2 text-[11px] text-gray-400">
                            <strong>User Agent:</strong> {{ $selectedLog->user_agent }}
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t flex justify-end">
                    <button wire:click="closeDetails" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
