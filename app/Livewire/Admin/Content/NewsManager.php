<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\NewsArticle;
use App\Services\PublicContentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class NewsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $categoryFilter = '';

    public bool $showTrashed = false;

    // Modal State
    public bool $showModal = false;

    public ?int $articleId = null;

    public string $title = '';

    public ?string $slug = null;

    public ?string $short_description = null;

    public ?string $content_html = null;

    public $featured_image_upload;

    public ?string $existing_featured_image = null;

    public ?string $category = 'Exam Notification';

    public ?string $author = 'ParikshaMitra Editorial';

    public ?string $publish_date = null;

    public ?string $expiry_date = null;

    public string $status = NewsArticle::STATUS_PUBLISHED;

    public bool $featured = false;

    public int $display_order = 0;

    public ?string $meta_title = null;

    public ?string $meta_description = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'content_html' => 'required|string',
            'featured_image_upload' => 'nullable|image|max:3072',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'status' => 'required|in:DRAFT,REVIEW,PUBLISHED,SCHEDULED,ARCHIVED',
            'featured' => 'boolean',
            'display_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
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
            'articleId',
            'title',
            'slug',
            'short_description',
            'content_html',
            'featured_image_upload',
            'existing_featured_image',
            'category',
            'author',
            'publish_date',
            'expiry_date',
            'status',
            'featured',
            'display_order',
            'meta_title',
            'meta_description',
        ]);
        $this->category = 'Exam Notification';
        $this->author = 'ParikshaMitra Editorial';
        $this->status = NewsArticle::STATUS_PUBLISHED;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $article = NewsArticle::withTrashed()->findOrFail($id);
        $this->articleId = $article->id;
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->short_description = $article->short_description;
        $this->content_html = $article->content_html;
        $this->existing_featured_image = $article->featured_image;
        $this->category = $article->category;
        $this->author = $article->author;
        $this->publish_date = $article->publish_date?->format('Y-m-d\TH:i');
        $this->expiry_date = $article->expiry_date?->format('Y-m-d\TH:i');
        $this->status = $article->status;
        $this->featured = (bool) $article->featured;
        $this->display_order = (int) $article->display_order;
        $this->meta_title = $article->meta_title;
        $this->meta_description = $article->meta_description;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $imagePath = $this->existing_featured_image;
        if ($this->featured_image_upload) {
            $imagePath = $this->featured_image_upload->store('news', 'public');
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug ?: null,
            'short_description' => $this->short_description,
            'content_html' => $this->content_html,
            'featured_image' => $imagePath,
            'category' => $this->category,
            'author' => $this->author,
            'publish_date' => $this->publish_date ?: now(),
            'expiry_date' => $this->expiry_date ?: null,
            'status' => $this->status,
            'featured' => $this->featured,
            'display_order' => $this->display_order,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'updated_by' => Auth::id(),
        ];

        if ($this->articleId) {
            $article = NewsArticle::withTrashed()->findOrFail($this->articleId);
            $oldData = $article->toArray();
            $article->update($data);

            AuditLog::record('NEWS_UPDATED', 'NEWS', (string) $article->id, $oldData, $article->toArray());
            session()->flash('message', "News article '{$this->title}' updated successfully.");
        } else {
            $data['created_by'] = Auth::id();
            $article = NewsArticle::create($data);

            AuditLog::record('NEWS_CREATED', 'NEWS', (string) $article->id, null, $article->toArray());
            session()->flash('message', "News article '{$this->title}' created successfully.");
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
    }

    public function toggleFeatured(int $id): void
    {
        $article = NewsArticle::findOrFail($id);
        $old = $article->featured;
        $article->featured = ! $article->featured;
        $article->save();

        AuditLog::record('NEWS_FEATURED_TOGGLED', 'NEWS', (string) $article->id, ['featured' => $old], ['featured' => $article->featured]);
        PublicContentService::clearAllCache();
    }

    public function delete(int $id): void
    {
        $article = NewsArticle::findOrFail($id);
        $title = $article->title;
        AuditLog::record('NEWS_SOFT_DELETED', 'NEWS', (string) $article->id, $article->toArray(), null);
        $article->delete();

        PublicContentService::clearAllCache();
        session()->flash('message', "News article '{$title}' moved to trash.");
    }

    public function restore(int $id): void
    {
        $article = NewsArticle::onlyTrashed()->findOrFail($id);
        $article->restore();
        AuditLog::record('NEWS_RESTORED', 'NEWS', (string) $article->id, null, $article->toArray());

        PublicContentService::clearAllCache();
        session()->flash('message', "News article '{$article->title}' restored successfully.");
    }

    public function forceDelete(int $id): void
    {
        $article = NewsArticle::onlyTrashed()->findOrFail($id);
        $title = $article->title;
        AuditLog::record('NEWS_PERMANENTLY_DELETED', 'NEWS', (string) $article->id, $article->toArray(), null);
        $article->forceDelete();

        PublicContentService::clearAllCache();
        session()->flash('message', "News article '{$title}' permanently deleted.");
    }

    public function render()
    {
        $query = $this->showTrashed ? NewsArticle::onlyTrashed() : NewsArticle::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('author', 'like', '%'.$this->search.'%')
                    ->orWhere('category', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->statusFilter) && ! $this->showTrashed) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->categoryFilter)) {
            $query->where('category', $this->categoryFilter);
        }

        $articles = $query->orderBy('display_order', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        $categories = NewsArticle::distinct()->pluck('category')->filter();

        return view('livewire.admin.content.news-manager', [
            'articles' => $articles,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
