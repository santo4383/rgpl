<?php

namespace App\Livewire\Admin\Content;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Services\PublicContentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $priorityFilter = '';

    // Form Modal State
    public bool $showModal = false;

    public ?int $announcementId = null;

    public string $title = '';

    public ?string $short_description = null;

    public ?string $content = null;

    public ?string $link = null;

    public ?string $button_text = 'Learn More';

    public string $priority = 'MEDIUM';

    public ?string $category = 'EXAM_ALERT';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public string $status = Announcement::STATUS_PUBLISHED;

    public bool $featured = false;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'priority' => 'required|in:URGENT,HIGH,MEDIUM,LOW',
            'category' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:DRAFT,REVIEW,PUBLISHED,SCHEDULED,EXPIRED,ARCHIVED',
            'featured' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'announcementId',
            'title',
            'short_description',
            'content',
            'link',
            'button_text',
            'priority',
            'category',
            'start_date',
            'end_date',
            'status',
            'featured',
        ]);
        $this->button_text = 'Learn More';
        $this->priority = 'MEDIUM';
        $this->status = Announcement::STATUS_PUBLISHED;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $this->announcementId = $announcement->id;
        $this->title = $announcement->title;
        $this->short_description = $announcement->short_description;
        $this->content = $announcement->content;
        $this->link = $announcement->link;
        $this->button_text = $announcement->button_text;
        $this->priority = $announcement->priority ?? 'MEDIUM';
        $this->category = $announcement->category;
        $this->start_date = $announcement->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $announcement->end_date?->format('Y-m-d\TH:i');
        $this->status = $announcement->status;
        $this->featured = (bool) $announcement->featured;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'short_description' => $this->short_description,
            'content' => $this->content,
            'link' => $this->link,
            'button_text' => $this->button_text,
            'priority' => $this->priority,
            'category' => $this->category,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'status' => $this->status,
            'featured' => $this->featured,
            'updated_by' => Auth::id(),
        ];

        if ($this->announcementId) {
            $announcement = Announcement::findOrFail($this->announcementId);
            $oldData = $announcement->toArray();
            $announcement->update($data);

            AuditLog::record('ANNOUNCEMENT_UPDATED', 'ANNOUNCEMENT', (string) $announcement->id, $oldData, $announcement->toArray());
            session()->flash('message', "Announcement '{$this->title}' updated successfully.");
        } else {
            $data['created_by'] = Auth::id();
            $announcement = Announcement::create($data);

            AuditLog::record('ANNOUNCEMENT_CREATED', 'ANNOUNCEMENT', (string) $announcement->id, null, $announcement->toArray());
            session()->flash('message', "Announcement '{$this->title}' created successfully.");
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $announcement = Announcement::findOrFail($id);
        $old = $announcement->status;
        $announcement->status = $newStatus;
        $announcement->updated_by = Auth::id();
        $announcement->save();

        AuditLog::record('ANNOUNCEMENT_STATUS_CHANGED', 'ANNOUNCEMENT', (string) $announcement->id, ['status' => $old], ['status' => $newStatus]);
        PublicContentService::clearAllCache();
        session()->flash('message', "Status updated to {$newStatus}.");
    }

    public function delete(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;
        AuditLog::record('ANNOUNCEMENT_DELETED', 'ANNOUNCEMENT', (string) $announcement->id, $announcement->toArray(), null);
        $announcement->delete();

        PublicContentService::clearAllCache();
        session()->flash('message', "Announcement '{$title}' deleted.");
    }

    public function render()
    {
        $query = Announcement::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('short_description', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->priorityFilter)) {
            $query->where('priority', $this->priorityFilter);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.content.announcement-manager', [
            'announcements' => $announcements,
        ])->layout('layouts.app');
    }
}
