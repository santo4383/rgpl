<?php

namespace App\Livewire\Admin\AuditLogs;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $moduleFilter = '';

    public string $actionFilter = '';

    public ?AuditLog $selectedLog = null;

    public bool $showDetailsModal = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedModuleFilter()
    {
        $this->resetPage();
    }

    public function updatedActionFilter()
    {
        $this->resetPage();
    }

    public function viewDetails(int $logId)
    {
        $this->selectedLog = AuditLog::with('user')->find($logId);
        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->selectedLog = null;
        $this->showDetailsModal = false;
    }

    public function render()
    {
        $query = AuditLog::with('user');

        if (! empty($this->moduleFilter)) {
            $query->where('module', $this->moduleFilter);
        }

        if (! empty($this->actionFilter)) {
            $query->where('action', 'like', '%'.$this->actionFilter.'%');
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('action', 'ilike', '%'.$this->search.'%')
                    ->orWhere('record_id', 'like', '%'.$this->search.'%')
                    ->orWhere('ip_address', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'ilike', '%'.$this->search.'%')
                            ->orWhere('mobile_number', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $logs = $query->latest('created_at')->paginate(20);

        // Distinct modules for dropdown
        $modules = ['AUTH', 'CONTENT', 'PAYMENTS', 'EXAMS', 'SETTINGS'];

        return view('livewire.admin.audit-logs.audit-log-index', [
            'logs' => $logs,
            'modules' => $modules,
        ])->layout('layouts.app');
    }
}
