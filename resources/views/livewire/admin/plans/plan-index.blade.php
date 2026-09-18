<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Subscription Plans') }}
            </h2>
            <a href="{{ route('admin.plans.create') }}" wire:navigate class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-xs">
                + {{ __('Create New Plan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-6 text-green-800 bg-green-50 border border-green-200 p-4 rounded-lg text-sm">
                    {{ session('message') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xs sm:rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 text-left">{{ __('Plan Name') }}</th>
                            <th class="px-6 py-3.5 text-left">{{ __('Price') }}</th>
                            <th class="px-6 py-3.5 text-left">{{ __('Duration') }}</th>
                            <th class="px-6 py-3.5 text-left">{{ __('Coverage') }}</th>
                            <th class="px-6 py-3.5 text-left">{{ __('Status') }}</th>
                            <th class="px-6 py-3.5 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    {{ $plan->name }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    &#8377;{{ number_format($plan->price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $plan->duration_days }} {{ __('days') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-700">
                                    @if($plan->is_all_courses)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-indigo-100 text-indigo-800">
                                            ★ {{ __('All Courses') }}
                                        </span>
                                    @else
                                        <span>{{ $plan->courses->pluck('name')->join(', ') ?: __('No courses assigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleActive({{ $plan->id }})" class="cursor-pointer">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3 font-medium text-xs">
                                    <a href="{{ route('admin.plans.edit', $plan) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 font-bold">
                                        {{ __('Edit') }}
                                    </a>
                                    <button wire:click="deletePlan({{ $plan->id }})" wire:confirm="Are you sure you want to delete this subscription plan?" class="text-red-600 hover:text-red-900">
                                        {{ __('Delete') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    {{ __('No subscription plans created yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $plans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
