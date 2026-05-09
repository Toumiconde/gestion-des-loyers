<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Parametre;

class PaymentConfirmationNotification extends Notification
{
    use Queueable;

    protected $paiement;

    public function __construct($paiement)
    {
        $this->paiement = $paiement;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        $settings = Parametre::all()->pluck('valeur', 'cle');
        
        if (($settings['alerte_email'] ?? 'on') === 'on') {
            $channels[] = 'mail';
        }

        if (($settings['alerte_sms'] ?? 'off') === 'on' && !empty($notifiable->telephone)) {
            $msg = "Merci {$notifiable->name}, votre paiement de " . number_format($this->paiement->montant, 0, ',', ' ') . " GNF a été reçu avec succès.";
            \Log::info("SMS Confirmation envoyé à {$notifiable->telephone} : " . $msg);
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $agence = Parametre::where('cle', 'nom_agence')->value('valeur') ?? 'GESTLOYER';
        $quittance = $this->paiement->quittance;

        return (new MailMessage)
                    ->subject('Confirmation de paiement - ' . $agence)
                    ->greeting('Merci ' . $notifiable->name . ' !')
                    ->line('Nous vous confirmons la réception de votre paiement pour le mois de ' . \Carbon\Carbon::parse($this->paiement->mois_concerne)->format('F Y') . '.')
                    ->line('Montant : ' . number_format($this->paiement->montant, 0, ',', ' ') . ' GNF')
                    ->line('Référence : ' . ($this->paiement->reference ?? 'N/A'))
                    ->action('Télécharger ma quittance', route('quittances.show', $quittance->id))
                    ->line('Votre quittance est désormais disponible dans votre espace personnel.')
                    ->salutation('Cordialement, l\'équipe ' . $agence);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Paiement de ' . $this->paiement->montant . ' GNF confirmé.',
            'type' => 'payment_confirmation',
            'paiement_id' => $this->paiement->id
        ];
    }
}
