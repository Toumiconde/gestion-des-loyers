<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthlyReportAvailable extends Notification
{
    use Queueable;

    protected $month;
    protected $year;

    /**
     * Create a new notification instance.
     */
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
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
        $url = route('reports.monthly', ['year' => $this->year, 'month' => $this->month]);
        
        return (new MailMessage)
                    ->subject("📊 Votre Relevé de Gestion - " . $this->month . "/" . $this->year)
                    ->greeting("Bonjour " . $notifiable->name . ",")
                    ->line("Votre relevé de gestion pour le mois de " . $this->month . " " . $this->year . " est désormais disponible dans votre espace client.")
                    ->line("Ce document récapitule vos loyers encaissés et les éventuels frais de maintenance déduits.")
                    ->action('Consulter mon Relevé', $url)
                    ->line('Merci de votre confiance en notre agence.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Relevé de Gestion Disponible",
            'message' => "Votre bilan pour le mois de {$this->month}/{$this->year} est prêt à être consulté.",
            'url' => route('reports.monthly', ['year' => $this->year, 'month' => $this->month]),
            'icon' => 'fa-file-invoice-dollar',
            'color' => 'indigo'
        ];
    }
}
