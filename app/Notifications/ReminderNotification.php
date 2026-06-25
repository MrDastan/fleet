<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $title,
        private string $message,
        private string $type,
        private ?int $vehicleId = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("MSD Fleet — {$this->title}")
            ->greeting("Salam {$notifiable->name},")
            ->line($this->message)
            ->action('Lihat dalam sistem', url('/reminders'))
            ->line('Sila ambil tindakan sebelum tarikh luput.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'vehicle_id' => $this->vehicleId,
        ];
    }
}
