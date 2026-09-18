<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\Page;
use App\Services\PublicContentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PageManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal State
    public bool $showModal = false;

    public ?int $pageId = null;

    public string $title = '';

    public string $slug = '';

    public string $content_html = '';

    public ?string $meta_title = null;

    public ?string $meta_description = null;

    public string $status = Page::STATUS_PUBLISHED;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.($this->pageId ?? 0),
            'content_html' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:PUBLISHED,DRAFT',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'pageId',
            'title',
            'slug',
            'content_html',
            'meta_title',
            'meta_description',
        ]);
        $this->status = Page::STATUS_PUBLISHED;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $page = Page::findOrFail($id);
        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content_html = $page->content_html;
        $this->meta_title = $page->meta_title;
        $this->meta_description = $page->meta_description;
        $this->status = $page->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content_html' => $this->content_html,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'status' => $this->status,
            'updated_by' => Auth::id(),
        ];

        if ($this->pageId) {
            $page = Page::findOrFail($this->pageId);
            $old = $page->toArray();
            $page->update($data);

            AuditLog::record('PAGE_UPDATED', 'PAGE', (string) $page->id, $old, $page->toArray());
            session()->flash('message', "Page '{$this->title}' updated successfully.");
        } else {
            $data['created_by'] = Auth::id();
            $page = Page::create($data);

            AuditLog::record('PAGE_CREATED', 'PAGE', (string) $page->id, null, $page->toArray());
            session()->flash('message', "Page '{$this->title}' created successfully.");
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        $page = Page::findOrFail($id);
        $title = $page->title;
        AuditLog::record('PAGE_DELETED', 'PAGE', (string) $page->id, $page->toArray(), null);
        $page->delete();

        PublicContentService::clearAllCache();
        session()->flash('message', "Page '{$title}' deleted.");
    }

    public function render()
    {
        $query = Page::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            });
        }

        $pages = $query->orderBy('title', 'asc')->paginate(10);

        return view('livewire.admin.content.page-manager', [
            'pages' => $pages,
        ])->layout('layouts.app');
    }
}
