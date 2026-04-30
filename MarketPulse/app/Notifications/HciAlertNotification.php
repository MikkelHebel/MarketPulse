<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HciAlertNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $ticker,
        private int $hci,
        private string $type
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticker' => $this->ticker,
            'hci' => $this->hci,
            'type' => $this->type,
            'message' => $this->type === 'hype' ? "{$this->ticker} is hyping! 🚀 HCI reached {$this->hci}" : "{$this->ticker} is crashing! 💀 HCI dropped to {$this->hci}",
        ];
    }
}
