<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProfileUpdated extends Notification
{
    use Queueable;

    protected $user;
    protected $message;

    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => $this->message,
            'type' => 'profile_update',
        ];
    }
}
