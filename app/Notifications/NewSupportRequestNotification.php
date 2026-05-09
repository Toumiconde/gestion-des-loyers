<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportRequestNotification extends Notification
{
    use Queueable;

    public $sender;
    public $message_content;

    /**
     * Create a new notification instance.
     */
    public function __construct($sender, $message_content)
    {
        $this->sender = $sender;
        $this->message_content = $message_content;
    }

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
            'message' => "🆘 Nouvelle demande de support de <strong>{$this->sender->name}</strong>",
            'user_id' => $this->sender->id,
            'user_name' => $this->sender->name,
            'type' => 'support'
        ];
    }
}
