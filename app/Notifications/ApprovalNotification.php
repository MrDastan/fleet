<?php

namespace App\Notifications;

use App\Models\VehicleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        private VehicleRequest $request,
        private string $action,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->action) {
            'submitted' => "Permohonan baharu: {$this->request->request_no}",
            'guard_approved' => "Disahkan penjaga: {$this->request->request_no}",
            'fleet_approved' => "Diluluskan: {$this->request->request_no}",
            'rejected' => "Ditolak: {$this->request->request_no}",
            default => "Kemaskini permohonan: {$this->request->request_no}",
        };

        return (new MailMessage)
            ->subject("MSD Fleet — {$subject}")
            ->greeting("Salam {$notifiable->name},")
            ->line($this->getMessage())
            ->action('Lihat permohonan', url('/approvals'))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Permohonan {$this->request->request_no}",
            'message' => $this->getMessage(),
            'action' => $this->action,
            'request_id' => $this->request->id,
            'request_no' => $this->request->request_no,
            'vehicle_plat' => $this->request->vehicle->plat,
        ];
    }

    private function getMessage(): string
    {
        return match ($this->action) {
            'submitted' => "{$this->request->requester->name} memohon kenderaan {$this->request->vehicle->plat} pada {$this->request->use_date->format('d M Y')}.",
            'guard_approved' => "Penjaga telah sahkan permohonan {$this->request->request_no}. Menunggu kelulusan Fleet.",
            'fleet_approved' => "Permohonan {$this->request->request_no} telah diluluskan. Kenderaan {$this->request->vehicle->plat} sedia untuk digunakan.",
            'rejected' => "Permohonan {$this->request->request_no} telah ditolak.",
            default => "Status permohonan {$this->request->request_no} dikemaskini.",
        };
    }
}
