<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function all(): Collection
    {
        return Project::query()->with('client')->latest()->get();
    }

    public function create(array $attributes): Project
    {
        return Project::query()->create([
            'client_id' => (int) ($attributes['client_id'] ?? 0),
            'name' => trim((string) ($attributes['name'] ?? '')),
            'status' => $attributes['status'] ?? 'in_progress',
            'budget' => (float) ($attributes['budget'] ?? 0),
            'due_date' => $attributes['due_date'] ?? null,
        ]);
    }

    public function count(): int
    {
        return Project::query()->count();
    }
}
