<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\HomepageStatistic;
use App\Services\PublicContentService;
use Livewire\Component;

class StatisticManager extends Component
{
    public $statistics;

    public bool $showModal = false;

    public ?int $statId = null;

    public string $title = '';

    public string $value = '';

    public string $stat_key = '';

    public string $icon = '';

    public string $description = '';

    public int $display_order = 0;

    public string $status = 'ACTIVE';

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->statistics = HomepageStatistic::orderBy('display_order', 'asc')->get();
    }

    public function openCreateModal()
    {
        $this->reset(['statId', 'title', 'value', 'stat_key', 'icon', 'description', 'display_order']);
        $this->status = 'ACTIVE';
        $this->display_order = count($this->statistics) + 1;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $stat = HomepageStatistic::findOrFail($id);
        $this->statId = $stat->id;
        $this->title = $stat->title;
        $this->value = $stat->value ?? '';
        $this->stat_key = $stat->stat_key ?? '';
        $this->icon = $stat->icon ?? '';
        $this->description = $stat->description ?? '';
        $this->display_order = $stat->display_order;
        $this->status = $stat->status;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:100',
            'value' => 'nullable|string|max:50',
            'stat_key' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $data = [
            'title' => $this->title,
            'value' => $this->value ?: null,
            'stat_key' => $this->stat_key ?: null,
            'icon' => $this->icon ?: null,
            'description' => $this->description ?: null,
            'display_order' => $this->display_order,
            'status' => $this->status,
        ];

        if ($this->statId) {
            $stat = HomepageStatistic::findOrFail($this->statId);
            $old = $stat->toArray();
            $stat->update($data);

            AuditLog::record('STATISTIC_UPDATED', 'STATISTICS', (string) $stat->id, $old, $stat->toArray());
            session()->flash('message', 'Statistic updated successfully.');
        } else {
            $stat = HomepageStatistic::create($data);
            AuditLog::record('STATISTIC_CREATED', 'STATISTICS', (string) $stat->id, null, $stat->toArray());
            session()->flash('message', 'New statistic created successfully.');
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
        $this->loadStats();
    }

    public function toggleStatus(int $id)
    {
        $stat = HomepageStatistic::findOrFail($id);
        $old = $stat->status;
        $stat->status = ($stat->status === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $stat->save();

        AuditLog::record('STATISTIC_TOGGLED', 'STATISTICS', (string) $stat->id, ['status' => $old], ['status' => $stat->status]);
        PublicContentService::clearAllCache();
        $this->loadStats();
    }

    public function delete(int $id)
    {
        $stat = HomepageStatistic::findOrFail($id);
        $old = $stat->toArray();
        $stat->delete();

        AuditLog::record('STATISTIC_DELETED', 'STATISTICS', (string) $id, $old, null);
        PublicContentService::clearAllCache();
        $this->loadStats();
        session()->flash('message', 'Statistic deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.content.statistic-manager')->layout('layouts.app');
    }
}
