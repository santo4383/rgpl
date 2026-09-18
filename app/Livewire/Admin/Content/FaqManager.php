<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\Faq;
use App\Services\PublicContentService;
use Livewire\Component;
use Livewire\WithPagination;

class FaqManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    // Modal State
    public bool $showModal = false;

    public ?int $faqId = null;

    public string $question = '';

    public string $answer_html = '';

    public string $category = 'GENERAL';

    public int $display_order = 0;

    public string $status = 'ACTIVE';

    public bool $is_featured_homepage = true;

    protected function rules(): array
    {
        return [
            'question' => 'required|string|max:255',
            'answer_html' => 'required|string',
            'category' => 'required|string|max:50',
            'display_order' => 'integer|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'is_featured_homepage' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'faqId',
            'question',
            'answer_html',
            'category',
            'display_order',
            'status',
            'is_featured_homepage',
        ]);
        $this->category = 'GENERAL';
        $this->status = 'ACTIVE';
        $this->is_featured_homepage = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $this->faqId = $faq->id;
        $this->question = $faq->question;
        $this->answer_html = $faq->answer_html;
        $this->category = $faq->category;
        $this->display_order = (int) $faq->display_order;
        $this->status = $faq->status;
        $this->is_featured_homepage = (bool) $faq->is_featured_homepage;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'question' => $this->question,
            'answer_html' => $this->answer_html,
            'category' => $this->category,
            'display_order' => $this->display_order,
            'status' => $this->status,
            'is_featured_homepage' => $this->is_featured_homepage,
        ];

        if ($this->faqId) {
            $faq = Faq::findOrFail($this->faqId);
            $oldData = $faq->toArray();
            $faq->update($data);

            AuditLog::record('FAQ_UPDATED', 'FAQ', (string) $faq->id, $oldData, $faq->toArray());
            session()->flash('message', "FAQ '{$this->question}' updated successfully.");
        } else {
            $faq = Faq::create($data);

            AuditLog::record('FAQ_CREATED', 'FAQ', (string) $faq->id, null, $faq->toArray());
            session()->flash('message', 'FAQ created successfully.');
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
    }

    public function toggleStatus(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $old = $faq->status;
        $faq->status = ($faq->status === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $faq->save();

        AuditLog::record('FAQ_STATUS_TOGGLED', 'FAQ', (string) $faq->id, ['status' => $old], ['status' => $faq->status]);
        PublicContentService::clearAllCache();
    }

    public function toggleHomepageFeatured(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $old = $faq->is_featured_homepage;
        $faq->is_featured_homepage = ! $faq->is_featured_homepage;
        $faq->save();

        AuditLog::record('FAQ_HOMEPAGE_FEATURED_TOGGLED', 'FAQ', (string) $faq->id, ['is_featured_homepage' => $old], ['is_featured_homepage' => $faq->is_featured_homepage]);
        PublicContentService::clearAllCache();
    }

    public function delete(int $id): void
    {
        $faq = Faq::findOrFail($id);
        AuditLog::record('FAQ_DELETED', 'FAQ', (string) $faq->id, $faq->toArray(), null);
        $faq->delete();

        PublicContentService::clearAllCache();
        session()->flash('message', 'FAQ deleted successfully.');
    }

    public function render()
    {
        $query = Faq::query();

        if (! empty($this->search)) {
            $query->where('question', 'like', '%'.$this->search.'%')
                ->orWhere('answer_html', 'like', '%'.$this->search.'%');
        }

        if (! empty($this->categoryFilter)) {
            $query->where('category', $this->categoryFilter);
        }

        $faqs = $query->orderBy('display_order', 'asc')->orderBy('id', 'asc')->paginate(15);
        $categories = Faq::distinct()->pluck('category')->filter();

        return view('livewire.admin.content.faq-manager', [
            'faqs' => $faqs,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
