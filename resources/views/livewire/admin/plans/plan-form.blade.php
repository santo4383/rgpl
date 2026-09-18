<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $plan ? __('Edit Subscription Plan') : __('Create Subscription Plan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-xl border border-gray-200 p-6 md:p-8">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Plan Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="e.g. MPSC 3-Month Complete Pass" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description (Optional)</label>
                        <textarea wire:model="description" rows="2" placeholder="Brief overview of what this plan includes" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Price (&#8377; INR) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" wire:model="price" placeholder="499.00" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Duration (in Days) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="duration_days" placeholder="30" class="w-full text-sm border-gray-300 rounded-lg shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            @error('duration_days') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Coverage Option -->
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="is_all_courses" class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-bold text-gray-800">{{ __('Grant Access to ALL Courses & Exams (Full Access Pass)') }}</span>
                        </label>

                        @if(!$is_all_courses)
                            <div class="pt-3 border-t border-gray-200">
                                <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wider">{{ __('Or Select Specific Course(s) for this Plan:') }}</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                    @foreach($courses as $c)
                                        <label class="flex items-center p-2 rounded border bg-white cursor-pointer hover:bg-gray-50 text-xs">
                                            <input type="checkbox" wire:model="selectedCourseIds" value="{{ $c->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 mr-2">
                                            <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedCourseIds') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-semibold text-gray-700">Active (Visible to Students)</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.plans.index') }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-xs">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-xs">
                            {{ __('Save Plan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
