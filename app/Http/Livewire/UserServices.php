<?php

namespace App\Http\Livewire;

use App\Models\Service;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * @property $deleteId
 */
class UserServices extends Component
{
    public Service $service;
    public bool $showServiceModal = false;
    public bool $showEditService = false;
    public bool $showConfirmDeleteService = false;

    protected function rules(): array
    {
        return [
            "service.name" => [
                "required",
                "max:255",
                "string",
            ],
            "service.description" => ["max:1000", "string"],
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function mount(): void
    {
        $this->service = $this->makeBlankService();
    }

    public function makeBlankService()
    {
        if (!Auth::check()) {
            abort(403);
        }
        return Service::make([]);
    }

    public function save(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->validate();
        $user = auth()->user();
        $this->service->user_id = $user->id;
        $this->service->save();
        $this->showServiceModal = false;
        $this->dispatchBrowserEvent("notify", "Service saved!");
    }

    public function addService(): void
    {
        $this->service = $this->makeBlankService();
        $this->showServiceModal = true;
    }

    public function edit(Service $service): void
    {
        if ($this->service->isNot($service)) {
            $this->service = $service;
        }
        $this->showServiceModal = true;
    }

    public function deleteId($id): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->deleteId = $id;
        $this->showConfirmDeleteService = true;
    }

    public function delete(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $service = Service::find($this->deleteId);
        // Delete the Service model instance
        $service->delete();
        $this->showConfirmDeleteService = false;
        $this->dispatchBrowserEvent("notify", "Service deleted!");
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $services = Auth::user()->services;
        return view('livewire.user-services', [
            'services' => $services,
        ]);
    }
}
