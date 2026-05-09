<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $isUrgent;

    public function __construct($subject, $message, $isUrgent = false)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->isUrgent = $isUrgent;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'broadcast',
            'title'   => ($this->isUrgent ? '🚨 RAPPEL URGENT' : '📢 Annonce Importante'),
            'message' => $this->subject,
            'details' => $this->message,
            'link'    => route('messages.index'),
        ];
    }
}
