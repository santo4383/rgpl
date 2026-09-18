<?php

namespace App\Livewire\Admin\Plans;

use App\Models\SubscriptionPlan;
use Livewire\Component;
use Livewire\WithPagination;

class PlanIndex extends Component
{
    use WithPagination;

    public function deletePlan($id)
    {
        $plan = SubscriptionPlan::find($id);
        if ($plan) {
            $plan->courses()->detach();
            $plan->delete();
            session()->flash('message', 'Subscription plan deleted successfully.');
        }
    }

    public function toggleActive($id)
    {
        $plan = SubscriptionPlan::find($id);
        if ($plan) {
            $plan->is_active = ! $plan->is_active;
            $plan->save();
        }
    }

    public function render()
    {
        $plans = SubscriptionPlan::with('courses')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->paginate(10);

        return view('livewire.admin.plans.plan-index', [
            'plans' => $plans,
        ])->layout('layouts.app');
    }
}
