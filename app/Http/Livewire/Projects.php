<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Projects extends Component
{
    public Project $project;
    public bool $showProjectModal = false;
    public $showConfirmDeleteProject = false;

    public $deleteId = "";

    protected function rules(): array
    {
        return [
            "project.name" => [
                "required",
                "max:255",
                "string",
            ],
            "project.link" => "required|url",
            "project.description" => ["required", "min:50", "max:1000", "string"],
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function mount(): void
    {
        $this->project = $this->makeBlankProject();
    }

    public function makeBlankProject(): Project
    {
        if (!Auth::check()) {
            abort(403);
        }
        return Project::make([]);
    }

    public function edit(Project $project): void
    {
        if ($this->project->isNot($project)) {
            $this->project = $project;
        }
        $this->showProjectModal = true;
    }

    public function addProject(): void
    {
        $this->project = $this->makeBlankProject();
        $this->showProjectModal = true;
    }

    public function save(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->validate();
        $user = auth()->user();
        $this->project->user_id = $user->id;
        $this->project->save();
        $this->showProjectModal = false;
        $this->dispatchBrowserEvent("notify", "Project saved!");
    }

    public function deleteId($id): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $this->deleteId = $id;
        $this->showConfirmDeleteProject = true;
    }

    public function delete(): void
    {
        if (!Auth::check()) {
            abort(403);
        }
        $project = Project::find($this->deleteId);
        // Delete the Project model instance
        $project->delete();
        $this->showConfirmDeleteProject = false;
        $this->dispatchBrowserEvent("notify", "Project deleted!");
    }

    public function render(): Factory|View|Application
    {
        $projects = Project::where("user_id", auth()->id())
            ->orderByDesc("created_at")
            ->get();
        return view(
            "livewire.projects",
            compact("projects")
        );
    }
}
