<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    public function all(): Collection
    {
        return Notification::query()->latest()->get();
    }

    public function create(array $attributes): Notification
    {
        return Notification::query()->create([
            'to' => trim((string) ($attributes['to'] ?? '')),
            'subject' => trim((string) ($attributes['subject'] ?? 'Nuevo contacto')),
            'message' => trim((string) ($attributes['message'] ?? '')),
            'channel' => $attributes['channel'] ?? 'email',
            'status' => $attributes['status'] ?? 'queued',
        ]);
    }
}
