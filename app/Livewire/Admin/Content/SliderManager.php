<?php

namespace App\Livewire\Admin\Content;

use App\Models\AuditLog;
use App\Models\HomepageSlider;
use App\Services\PublicContentService;
use Livewire\Component;
use Livewire\WithFileUploads;

class SliderManager extends Component
{
    use WithFileUploads;

    public $sliders;

    public bool $showModal = false;

    public ?int $sliderId = null;

    public string $title = '';

    public string $subtitle = '';

    public string $description = '';

    public string $button_text = '';

    public string $button_url = '';

    public string $secondary_button_text = '';

    public string $secondary_button_url = '';

    public int $display_order = 0;

    public bool $is_active = true;

    public $start_date = null;

    public $end_date = null;

    public $desktop_image_upload;

    public $mobile_image_upload;

    public ?string $existing_desktop_image = null;

    public ?string $existing_mobile_image = null;

    public function mount()
    {
        $this->loadSliders();
    }

    public function loadSliders()
    {
        $this->sliders = HomepageSlider::orderBy('display_order', 'asc')->get();
    }

    public function openCreateModal()
    {
        $this->reset(['sliderId', 'title', 'subtitle', 'description', 'button_text', 'button_url', 'secondary_button_text', 'secondary_button_url', 'display_order', 'is_active', 'start_date', 'end_date', 'desktop_image_upload', 'mobile_image_upload', 'existing_desktop_image', 'existing_mobile_image']);
        $this->is_active = true;
        $this->display_order = count($this->sliders) + 1;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $slider = HomepageSlider::findOrFail($id);
        $this->sliderId = $slider->id;
        $this->title = $slider->title;
        $this->subtitle = $slider->subtitle ?? '';
        $this->description = $slider->description ?? '';
        $this->button_text = $slider->button_text ?? '';
        $this->button_url = $slider->button_url ?? '';
        $this->secondary_button_text = $slider->secondary_button_text ?? '';
        $this->secondary_button_url = $slider->secondary_button_url ?? '';
        $this->display_order = $slider->display_order;
        $this->is_active = $slider->is_active;
        $this->start_date = $slider->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $slider->end_date?->format('Y-m-d\TH:i');
        $this->existing_desktop_image = $slider->desktop_image;
        $this->existing_mobile_image = $slider->mobile_image;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'secondary_button_text' => 'nullable|string|max:100',
            'secondary_button_url' => 'nullable|string|max:255',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'desktop_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'mobile_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $desktopPath = $this->existing_desktop_image;
        if ($this->desktop_image_upload) {
            $desktopPath = $this->desktop_image_upload->store('sliders', 'public');
        }

        $mobilePath = $this->existing_mobile_image;
        if ($this->mobile_image_upload) {
            $mobilePath = $this->mobile_image_upload->store('sliders', 'public');
        }

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle ?: null,
            'description' => $this->description ?: null,
            'button_text' => $this->button_text ?: null,
            'button_url' => $this->button_url ?: null,
            'secondary_button_text' => $this->secondary_button_text ?: null,
            'secondary_button_url' => $this->secondary_button_url ?: null,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'desktop_image' => $desktopPath,
            'mobile_image' => $mobilePath,
        ];

        if ($this->sliderId) {
            $slider = HomepageSlider::findOrFail($this->sliderId);
            $old = $slider->toArray();
            $slider->update($data);

            AuditLog::record('SLIDER_UPDATED', 'SLIDERS', (string) $slider->id, $old, $slider->toArray());
            session()->flash('message', 'Slider updated successfully.');
        } else {
            $slider = HomepageSlider::create($data);
            AuditLog::record('SLIDER_CREATED', 'SLIDERS', (string) $slider->id, null, $slider->toArray());
            session()->flash('message', 'New slider created successfully.');
        }

        PublicContentService::clearAllCache();
        $this->showModal = false;
        $this->loadSliders();
    }

    public function toggleActive(int $id)
    {
        $slider = HomepageSlider::findOrFail($id);
        $old = $slider->is_active;
        $slider->is_active = ! $slider->is_active;
        $slider->save();

        AuditLog::record('SLIDER_TOGGLED', 'SLIDERS', (string) $slider->id, ['is_active' => $old], ['is_active' => $slider->is_active]);
        PublicContentService::clearAllCache();
        $this->loadSliders();
    }

    public function delete(int $id)
    {
        $slider = HomepageSlider::findOrFail($id);
        $old = $slider->toArray();
        $slider->delete();

        AuditLog::record('SLIDER_DELETED', 'SLIDERS', (string) $id, $old, null);
        PublicContentService::clearAllCache();
        $this->loadSliders();
        session()->flash('message', 'Slider deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.content.slider-manager')->layout('layouts.app');
    }
}
