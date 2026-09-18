<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessageManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    // Message View Modal State
    public bool $showModal = false;

    public ?ContactMessage $selectedMessage = null;

    public string $status = 'NEW';

    public ?int $assigned_to = null;

    public ?string $admin_notes = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function viewMessage(int $id): void
    {
        $this->selectedMessage = ContactMessage::with('assignedTo')->findOrFail($id);
        $this->status = $this->selectedMessage->status;
        $this->assigned_to = $this->selectedMessage->assigned_to;
        $this->admin_notes = $this->selectedMessage->admin_notes;

        if ($this->selectedMessage->status === ContactMessage::STATUS_NEW) {
            $this->selectedMessage->status = ContactMessage::STATUS_IN_PROGRESS;
            $this->selectedMessage->save();
            $this->status = ContactMessage::STATUS_IN_PROGRESS;
        }

        $this->showModal = true;
    }

    public function updateMessage(): void
    {
        if (! $this->selectedMessage) {
            return;
        }

        $old = $this->selectedMessage->toArray();
        $this->selectedMessage->update([
            'status' => $this->status,
            'assigned_to' => $this->assigned_to ?: null,
            'admin_notes' => $this->admin_notes,
        ]);

        AuditLog::record('CONTACT_MESSAGE_UPDATED', 'CONTACT_MESSAGE', (string) $this->selectedMessage->id, $old, $this->selectedMessage->toArray());
        session()->flash('message', 'Inquiry notes and status updated.');
        $this->showModal = false;
    }

    public function markAsSpam(int $id): void
    {
        $msg = ContactMessage::findOrFail($id);
        $old = $msg->status;
        $msg->status = ContactMessage::STATUS_SPAM;
        $msg->save();

        AuditLog::record('CONTACT_MESSAGE_MARKED_SPAM', 'CONTACT_MESSAGE', (string) $msg->id, ['status' => $old], ['status' => 'SPAM']);
        session()->flash('message', 'Message flagged as SPAM.');
    }

    public function delete(int $id): void
    {
        $msg = ContactMessage::findOrFail($id);
        AuditLog::record('CONTACT_MESSAGE_DELETED', 'CONTACT_MESSAGE', (string) $msg->id, $msg->toArray(), null);
        $msg->delete();

        session()->flash('message', 'Contact message deleted.');
    }

    public function render()
    {
        $query = ContactMessage::with('assignedTo');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('mobile', 'like', '%'.$this->search.'%')
                    ->orWhere('subject', 'like', '%'.$this->search.'%')
                    ->orWhere('message', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        } else {
            // Hide spam by default unless filtered
            $query->where('status', '!=', ContactMessage::STATUS_SPAM);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(12);
        $admins = User::all();

        return view('livewire.admin.content.contact-message-manager', [
            'messages' => $messages,
            'admins' => $admins,
        ])->layout('layouts.app');
    }
}
