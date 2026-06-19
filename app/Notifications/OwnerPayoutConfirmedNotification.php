<?php

namespace App\Notifications;

use App\Models\Bilan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OwnerPayoutConfirmedNotification extends Notification
{
    use Queueable;

    protected $bilan;

    public function __construct(Bilan $bilan)
    {
        $this->bilan = $bilan;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $moisLabel = \Carbon\Carbon::create(null, $this->bilan->mois)->translatedFormat('F Y');
        $url = route('reversements.show', $this->bilan->id);

        return (new MailMessage)
                    ->subject("💰 Confirmation de Reversement - " . $moisLabel)
                    ->greeting("Bonjour " . $notifiable->name . ",")
                    ->line("Nous avons le plaisir de vous informer que le reversement de vos loyers pour le mois de **" . $moisLabel . "** a été effectué.")
                    ->line("Détails du versement :")
                    ->line("- Montant Net : **" . number_format($this->bilan->montant_net, 0, ',', ' ') . " GNF**")
                    ->line("- Mode de paiement : " . ucfirst($this->bilan->mode_paiement))
                    ->line("- Référence : " . ($this->bilan->ref_virement ?: 'Virement Bancaire'))
                    ->action('Consulter le Bordereau', $url)
                    ->line('Le montant devrait apparaître sur votre compte sous 24h à 48h selon les délais bancaires.')
                    ->line('Merci de votre confiance.');
    }

    public function toArray(object $notifiable): array
    {
        $moisLabel = \Carbon\Carbon::create(null, $this->bilan->mois)->translatedFormat('F Y');
        
        return [
            'title' => "Reversement Effectué",
            'message' => "Votre reversement de " . number_format($this->bilan->montant_net, 0, ',', ' ') . " GNF pour {$moisLabel} a été validé.",
            'url' => route('reversements.show', $this->bilan->id),
            'icon' => 'fa-hand-holding-dollar',
            'color' => 'emerald'
        ];
    }
}
