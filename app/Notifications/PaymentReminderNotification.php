<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Parametre;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    protected $contrat;
    protected $montant;
    protected $isAnnual;

    public function __construct($contrat, $montant, $isAnnual = false)
    {
        $this->contrat = $contrat;
        $this->montant = $montant;
        $this->isAnnual = $isAnnual;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        $settings = Parametre::all()->pluck('valeur', 'cle');
        
        if (($settings['alerte_email'] ?? 'on') === 'on') {
            $channels[] = 'mail';
        }

        if (($settings['alerte_sms'] ?? 'off') === 'on' && !empty($notifiable->telephone)) {
            $msg = $this->isAnnual 
                ? "Cher {$notifiable->name}, votre paiement annuel se termine bientôt. Prévoyez votre renouvellement (mensuel ou annuel)."
                : "Rappel : Votre loyer de {$this->montant} GNF est attendu dans 5 jours.";
            \Log::info("SMS Rappel envoyé à {$notifiable->telephone} : " . $msg);
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $agence = Parametre::where('cle', 'nom_agence')->value('valeur') ?? 'GESTLOYER';

        if ($this->isAnnual) {
            return (new MailMessage)
                ->subject('Fin de votre période annuelle - ' . $agence)
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Votre période de paiement annuel pour le bien « ' . $this->contrat->bien->nom . ' » arrive à son terme dans 15 jours.')
                ->line('Vous avez désormais le choix pour la période suivante :')
                ->line('- Recommencer un paiement annuel (pour être tranquille toute l\'année).')
                ->line('- Repasser à un paiement mensuel classique.')
                ->action('Choisir mon mode de paiement', url('/'))
                ->line('Merci de nous faire part de votre choix rapidement.')
                ->salutation('Cordialement, l\'équipe ' . $agence);
        }

        return (new MailMessage)
                    ->subject('Rappel de paiement - ' . $agence)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Ceci est un rappel concernant le paiement de votre loyer.')
                    ->line('Montant dû : ' . number_format($this->montant, 0, ',', ' ') . ' GNF')
                    ->line('Bien concerné : ' . $this->contrat->bien->nom)
                    ->action('Consulter mon espace', url('/'))
                    ->line('Merci de régulariser votre situation dans les plus brefs délais.')
                    ->salutation('Cordialement, l\'équipe ' . $agence);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Rappel de paiement envoyé pour un montant de ' . $this->montant . ' GNF',
            'type' => 'payment_reminder',
            'contrat_id' => $this->contrat->id
        ];
    }
}
