<?php

namespace App\Http\Livewire;

use App\Models\Certificate;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property User|Authenticatable|null $user
 */
class Certificates extends Component
{
    public Certificate $certificate;
    public $showCertificateModal = false;
    public $showConfirmDeleteCertificate = false;
    public $deleteId = "";

    protected function rules(): array
    {
        return [
            "certificate.name" => [
                "required",
                "max:255",
                "string",
                "regex:/^[a-zA-Z\s]+$/",
            ],
            "certificate.organization" => [
                "required",
                "max:255",
                "string",
                "regex:/^[a-zA-Z\s]+$/",
            ],
            "certificate.credentialID" => "nullable|string",
            "certificate.credentialURL" => "nullable|url",
            "certificate.description" => ["required", "min:50", "max:1000", "string"],
            "certificate.month_started" => "required|numeric",
            "certificate.month_ended" =>
                "nullable|numeric|required_with:certificate.year_ended",
            "certificate.year_started" => "required|numeric",
            "certificate.year_ended" =>
                "nullable|numeric|gt:certificate.year_started|required_with:certificate.month_ended",
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function mount(): void
    {
        $this->certificate = $this->makeBlankCertificate();
    }

    public function makeBlankCertificate()
    {
        if (!Auth::check()) {
            abort(403);
        }
        return Certificate::make([]);
    }

    public function save(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->validate();
        $user = auth()->user();
        $this->certificate->user_id = $user->id;
        $this->certificate->save();
        $this->showCertificateModal = false;
        $this->dispatchBrowserEvent("notify", "Certificate saved!");
    }

    public function addCertificate(): void
    {
        $this->certificate = $this->makeBlankCertificate();
        $this->showCertificateModal = true;
    }

    public function edit(Certificate $certificate): void
    {
        if ($this->certificate->isNot($certificate)) {
            $this->certificate = $certificate;
        }
        $this->showCertificateModal = true;
    }

    public function deleteId($id): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->deleteId = $id;
        $this->showConfirmDeleteCertificate = true;
    }

    public function delete(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $workExperience = Certificate::find($this->deleteId);
        // Delete the Experience model instance
        $workExperience->delete();
        $this->showConfirmDeleteCertificate = false;
        $this->dispatchBrowserEvent("notify", "Certificate deleted!");
    }

    public function render(): Factory|View|Application
    {
        $certificates = Certificate::where("user_id", auth()->id())
            ->orderByDesc("created_at")
            ->get();
        return view(
            "livewire.certificates",
            compact("certificates")
        );
    }
}
