<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Parametre;

class OwnerPaymentReceivedNotification extends Notification
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

        return $channels;
    }

    public function toMail($notifiable)
    {
        $agence = Parametre::where('cle', 'nom_agence')->value('valeur') ?? 'GESTLOYER';
        $locataire = $this->paiement->contrat->locataire;
        $bien = $this->paiement->contrat->bien;

        return (new MailMessage)
                    ->subject('Nouveau loyer encaissé - ' . $agence)
                    ->greeting('Bonjour ' . $notifiable->name . ' !')
                    ->line('Nous vous informons qu\'un paiement a été encaissé pour votre bien : **' . $bien->libelle . '**.')
                    ->line('Locataire : ' . $locataire->prenom . ' ' . $locataire->nom)
                    ->line('Période : ' . \Carbon\Carbon::parse($this->paiement->mois_concerne)->format('F Y'))
                    ->line('Montant collecté : **' . number_format($this->paiement->montant, 0, ',', ' ') . ' GNF**')
                    ->action('Consulter mes finances', route('paiements.index'))
                    ->line('Ce montant sera pris en compte dans votre prochain relevé de gestion mensuel.')
                    ->salutation('Cordialement, l\'équipe ' . $agence);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Nouveau loyer encaissé pour ' . $this->paiement->contrat->bien->libelle . ' (' . number_format($this->paiement->montant, 0, ',', ' ') . ' GNF).',
            'type' => 'owner_payment_received',
            'paiement_id' => $this->paiement->id
        ];
    }
}
