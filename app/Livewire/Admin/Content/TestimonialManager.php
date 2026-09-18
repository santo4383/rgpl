<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\Testimonial;
use App\Services\PublicContentService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TestimonialManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    // Modal State
    public bool $showModal = false;

    public ?int $testimonialId = null;

    public string $name = '';

    public ?string $designation = null;

    public ?string $field = 'MPSC';

    public string $content = '';

    public $photo_upload;

    public ?string $existing_photo = null;

    public int $rating = 5;

    public string $status = Testimonial::STATUS_PUBLISHED;

    public bool $featured = true;

    public int $display_order = 0;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'designation' => 'nullable|string|max:150',
            'field' => 'nullable|string|max:100',
            'content' => 'required|string|max:1000',
            'photo_upload' => 'nullable|image|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:DRAFT,APPROVED,PUBLISHED,ARCHIVED',
            'featured' => 'boolean',
            'display_order' => 'integer|min:0',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'testimonialId',
            'name',
            'designation',
            'field',
            'content',
            'photo_upload',
            'existing_photo',
            'rating',
            'status',
            'featured',
            'display_order',
        ]);
        $this->field = 'MPSC';
        $this->rating = 5;
        $this->status = Testimonial::STATUS_PUBLISHED;
        $this->featured = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $t = Testimonial::findOrFail($id);
        $this->testimonialId = $t->id;
        $this->name = $t->name;
        $this->designation = $t->designation;
        $this->field = $t->field;
        $this->content = $t->content;
        $this->existing_photo = $t->photo;
        $this->rating = (int) $t->rating;
        $this->status = $t->status;
        $this->featured = (bool) $t->featured;
        $this->display_order = (int) $t->display_order;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $photoPath = $this->existing_photo;
        if ($this->photo_upload) {
            $photoPath = $this->photo_upload->store('testimonials', 'public');
        }

        $data = [
            'name' => $this->name,
            'designation' => $this->designation,
            'field' => $this->field,
            'content' => $this->content,
            'photo' => $photoPath,
            'rating' => $this->rating,
            'status' => $this->status,
            'featured' => $this->featured,
            'display_order' => $this->display_order,
        ];

        if ($this->testimonialId) {
            $t = Testimonial::findOrFail($this->testimonialId);
            $old = $t->toArray();
            $t->update($data);

            AuditLog::record('TESTIMONIAL_UPDATED', 'TESTIMONIAL', (string) $t->id, $old, $t->toArray());
            session()->flash('message', "Testimonial from '{$this->name}' updated.");
        } else {
            $t = Testimonial::create($data);

            AuditLog::record('TESTIMONIAL_CREATED', 'TESTIMONIAL', (string) $t->id, null, $t->toArray());
            session()->flash('message', 'Testimonial created successfully.');
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
    }

    public function toggleFeatured(int $id): void
    {
        $t = Testimonial::findOrFail($id);
        $old = $t->featured;
        $t->featured = ! $t->featured;
        $t->save();

        AuditLog::record('TESTIMONIAL_FEATURED_TOGGLED', 'TESTIMONIAL', (string) $t->id, ['featured' => $old], ['featured' => $t->featured]);
        PublicContentService::clearAllCache();
    }

    public function updateStatus(int $id, string $status): void
    {
        $t = Testimonial::findOrFail($id);
        $old = $t->status;
        $t->status = $status;
        $t->save();

        AuditLog::record('TESTIMONIAL_STATUS_CHANGED', 'TESTIMONIAL', (string) $t->id, ['status' => $old], ['status' => $status]);
        PublicContentService::clearAllCache();
        session()->flash('message', "Status updated to {$status}.");
    }

    public function delete(int $id): void
    {
        $t = Testimonial::findOrFail($id);
        $name = $t->name;
        AuditLog::record('TESTIMONIAL_DELETED', 'TESTIMONIAL', (string) $t->id, $t->toArray(), null);
        $t->delete();

        PublicContentService::clearAllCache();
        session()->flash('message', "Testimonial from '{$name}' deleted.");
    }

    public function render()
    {
        $query = Testimonial::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('designation', 'like', '%'.$this->search.'%')
                    ->orWhere('field', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $testimonials = $query->orderBy('display_order', 'asc')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.content.testimonial-manager', [
            'testimonials' => $testimonials,
        ])->layout('layouts.app');
    }
}
