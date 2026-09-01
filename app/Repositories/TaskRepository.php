<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function all(): Collection
    {
        return Task::query()->with('project')->latest()->get();
    }

    public function create(array $attributes): Task
    {
        return Task::query()->create([
            'project_id' => (int) ($attributes['project_id'] ?? 0),
            'title' => trim((string) ($attributes['title'] ?? '')),
            'assignee' => isset($attributes['assignee']) ? trim((string) $attributes['assignee']) : null,
            'priority' => $attributes['priority'] ?? 'medium',
            'status' => $attributes['status'] ?? 'pending',
        ]);
    }

    public function count(): int
    {
        return Task::query()->count();
    }
}
