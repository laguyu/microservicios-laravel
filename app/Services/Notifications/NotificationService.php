<?php

namespace App\Services\Notifications;

use App\Repositories\NotificationRepository;
use InvalidArgumentException;

class NotificationService
{
    public function __construct(protected NotificationRepository $notificationRepository) {}

    public function getNotifications(): array
    {
        return $this->notificationRepository->all()->map(fn ($notification) => [
            'id' => $notification->id,
            'to' => $notification->to,
            'subject' => $notification->subject,
            'message' => $notification->message,
            'status' => $notification->status,
            'channel' => $notification->channel,
        ])->all();
    }

    public function sendContact(array $payload): array
    {
        $to = trim((string) ($payload['to'] ?? ''));
        $subject = trim((string) ($payload['subject'] ?? 'Nuevo contacto'));
        $message = trim((string) ($payload['message'] ?? ''));

        if ($to === '' || $message === '') {
            throw new InvalidArgumentException('to y message son obligatorios.');
        }

        $notification = $this->notificationRepository->create([
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'channel' => $payload['channel'] ?? 'email',
            'status' => $payload['status'] ?? 'queued',
        ]);

        return [
            'id' => $notification->id,
            'status' => $notification->status,
            'channel' => $notification->channel,
            'to' => $notification->to,
            'subject' => $notification->subject,
            'message' => $notification->message,
        ];
    }
}
