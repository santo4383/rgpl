<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- HEADER & ACTIONS -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Admin User Management</h1>
                <p class="text-xs text-gray-500 mt-1">Super Admin control center to provision, manage, and assign roles to administrative personnel.</p>
            </div>
            <div>
                <button
                    wire:click="openCreateModal"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-[0.98]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Admin User</span>
                </button>
            </div>
        </div>

        <!-- FLASH MESSAGES -->
        @if (session()->has('message'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- SEARCH BAR -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-4">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, email, or mobile..."
                    class="block w-full pl-10 pr-3.5 py-2 text-xs rounded-xl border border-gray-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition"
                />
            </div>
        </div>

        <!-- ADMIN USERS TABLE -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50/75 text-[11px] uppercase tracking-wider text-gray-500 font-bold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">Administrator</th>
                            <th scope="col" class="px-3 py-3.5">Contact Details</th>
                            <th scope="col" class="px-3 py-3.5">Assigned Role</th>
                            <th scope="col" class="px-3 py-3.5">Joined</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-xs">
                        @forelse ($adminUsers as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 pl-6 pr-3 font-medium text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            @if ($user->id === auth()->id())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Current User
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-gray-600">
                                    <div>{{ $user->email }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $user->mobile_number }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    @foreach ($user->roles as $role)
                                        @if ($role->name === 'SUPER_ADMIN')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                SUPER ADMIN
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                ADMIN
                                            </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="px-3 py-4 text-gray-500 text-[11px]">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-4 pl-3 pr-6 text-right space-x-2">
                                    <button
                                        wire:click="openEditModal({{ $user->id }})"
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold text-xs"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        wire:click="openPasswordModal({{ $user->id }})"
                                        type="button"
                                        class="text-amber-600 hover:text-amber-900 font-bold text-xs"
                                    >
                                        Reset Pass
                                    </button>

                                    @if ($user->id !== auth()->id())
                                        <button
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Are you sure you want to remove administrative privileges for {{ $user->name }}?"
                                            type="button"
                                            class="text-red-600 hover:text-red-900 font-bold text-xs"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    No administrative users found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($adminUsers->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $adminUsers->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE / EDIT MODAL -->
        @if ($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-gray-200 overflow-hidden animate-in fade-in zoom-in duration-150">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $isEditing ? 'Edit Admin User' : 'Create New Admin User' }}
                        </h3>
                        <button wire:click="$set('showModal', false)" type="button" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save" class="p-6 space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                            <input
                                type="text"
                                wire:model="name"
                                required
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            @error('name') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email Address</label>
                            <input
                                type="email"
                                wire:model="email"
                                required
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            @error('email') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mobile Number</label>
                            <input
                                type="text"
                                wire:model="mobile_number"
                                required
                                placeholder="10-digit mobile number"
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            @error('mobile_number') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Password {{ $isEditing ? '(Leave blank to retain current)' : '' }}
                            </label>
                            <input
                                type="password"
                                wire:model="password"
                                {{ $isEditing ? '' : 'required' }}
                                minlength="8"
                                placeholder="Minimum 8 characters"
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            @error('password') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Administrative Role</label>
                            <select
                                wire:model="role"
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option value="ADMIN">ADMIN (Standard administrative staff)</option>
                                <option value="SUPER_ADMIN">SUPER_ADMIN (Full platform ownership &amp; user management)</option>
                            </select>
                            @error('role') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button
                                wire:click="$set('showModal', false)"
                                type="button"
                                class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs"
                            >
                                {{ $isEditing ? 'Update Admin' : 'Save Admin' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- PASSWORD RESET MODAL -->
        @if ($showPasswordModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Reset Admin Password</h3>
                        <button wire:click="$set('showPasswordModal', false)" type="button" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="resetPassword" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">New Password</label>
                            <input
                                type="password"
                                wire:model="newPassword"
                                required
                                minlength="8"
                                placeholder="Enter new password (min 8 chars)"
                                class="w-full text-xs rounded-xl border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            @error('newPassword') <span class="text-red-600 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3">
                            <button
                                wire:click="$set('showPasswordModal', false)"
                                type="button"
                                class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs"
                            >
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
