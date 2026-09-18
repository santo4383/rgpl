<?php

namespace App\Livewire\Admin\Users;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUserManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal state
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?int $editingUserId = null;

    // Form fields
    public string $name = '';

    public string $email = '';

    public string $mobile_number = '';

    public string $password = '';

    public string $role = 'ADMIN';

    // Password reset modal state
    public bool $showPasswordModal = false;

    public ?int $passwordResetUserId = null;

    public string $newPassword = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'mobile_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'mobile_number')->ignore($this->editingUserId),
            ],
            'password' => [$this->isEditing ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:ADMIN,SUPER_ADMIN'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'email', 'mobile_number', 'password', 'editingUserId', 'isEditing']);
        $this->role = 'ADMIN';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::with('roles')->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->mobile_number = $user->mobile_number;
        $this->password = '';
        $this->role = $user->hasRole('SUPER_ADMIN') ? 'SUPER_ADMIN' : 'ADMIN';
        $this->isEditing = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN'], [
            'description' => 'Administrator with platform management access',
        ]);
        $superAdminRole = Role::firstOrCreate(['name' => 'SUPER_ADMIN'], [
            'description' => 'Super Administrator with full platform access',
        ]);

        $selectedRole = $this->role === 'SUPER_ADMIN' ? $superAdminRole : $adminRole;

        if ($this->isEditing && $this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $oldValues = [
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'role' => $user->roles->pluck('name')->implode(', '),
            ];

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'mobile_number' => $this->mobile_number,
            ];

            if (! empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);

            // Sync role
            $user->roles()->sync([$selectedRole->id]);

            AuditLog::record(
                action: 'UPDATE',
                module: 'ADMIN_USERS',
                recordId: (string) $user->id,
                oldValues: $oldValues,
                newValues: [
                    'name' => $this->name,
                    'email' => $this->email,
                    'mobile_number' => $this->mobile_number,
                    'role' => $this->role,
                ],
            );

            session()->flash('message', 'Admin user updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'mobile_number' => $this->mobile_number,
                'password' => Hash::make($this->password),
            ]);

            $user->roles()->attach($selectedRole->id);

            AuditLog::record(
                action: 'CREATE',
                module: 'ADMIN_USERS',
                recordId: (string) $user->id,
                oldValues: null,
                newValues: [
                    'name' => $this->name,
                    'email' => $this->email,
                    'mobile_number' => $this->mobile_number,
                    'role' => $this->role,
                ],
            );

            session()->flash('message', 'New admin user created successfully.');
        }

        $this->showModal = false;
        $this->reset(['name', 'email', 'mobile_number', 'password', 'editingUserId', 'isEditing']);
    }

    public function openPasswordModal(int $userId): void
    {
        $this->passwordResetUserId = $userId;
        $this->newPassword = '';
        $this->resetValidation();
        $this->showPasswordModal = true;
    }

    public function resetPassword(): void
    {
        $this->validate([
            'newPassword' => ['required', 'string', 'min:8'],
        ]);

        if (! $this->passwordResetUserId) {
            return;
        }

        $user = User::findOrFail($this->passwordResetUserId);
        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        AuditLog::record(
            action: 'RESET_PASSWORD',
            module: 'ADMIN_USERS',
            recordId: (string) $user->id,
            oldValues: null,
            newValues: ['reset_by' => Auth::user()->email],
        );

        session()->flash('message', "Password for {$user->name} was reset successfully.");
        $this->showPasswordModal = false;
        $this->reset(['newPassword', 'passwordResetUserId']);
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Auth::id()) {
            session()->flash('error', 'You cannot delete your own administrative account.');

            return;
        }

        $user = User::findOrFail($userId);

        AuditLog::record(
            action: 'DELETE',
            module: 'ADMIN_USERS',
            recordId: (string) $user->id,
            oldValues: [
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
            ],
            newValues: null,
        );

        $user->roles()->detach();
        $user->delete();

        session()->flash('message', 'Admin user deleted successfully.');
    }

    public function render()
    {
        // Fetch all users who have an administrative role
        $query = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['SUPER_ADMIN', 'ADMIN']);
        })->with('roles');

        if (! empty($this->search)) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('mobile_number', 'like', $term);
            });
        }

        $adminUsers = $query->latest()->paginate(10);

        return view('livewire.admin.users.admin-user-manager', [
            'adminUsers' => $adminUsers,
        ])->layout('layouts.app', ['header' => 'Admin Users Management']);
    }
}
