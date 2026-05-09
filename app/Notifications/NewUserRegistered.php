<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserRegistered extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nouvel utilisateur inscrit via Google')
                    ->greeting('Bonjour Admin !')
                    ->line('Un nouvel utilisateur vient de se connecter pour la première fois via Google.')
                    ->line('Nom : ' . $this->user->name)
                    ->line('Email : ' . $this->user->email)
                    ->action('Voir le profil', url('/dashboard'))
                    ->line('N\'oubliez pas de lier cet utilisateur à une fiche locataire ou propriétaire si nécessaire.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle inscription Google',
            'message' => $this->user->name . ' s\'est inscrit sur la plateforme.',
            'user_id' => $this->user->id,
            'type' => 'info'
        ];
    }
}
