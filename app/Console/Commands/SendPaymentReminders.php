<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * Signature de la commande.
     */
    protected $signature = 'app:send-payment-reminders';

    /**
     * Description de la commande.
     */
    protected $description = 'Envoie un message de rappel aux locataires 5 jours avant leur échéance de loyer.';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $this->info('━━━ Envoi des rappels Automatiques (SMS & Email) ━━━');

        $today = Carbon::today();
        $reminderDate = $today->copy()->addDays(5);

        // Récupérer les réglages globaux
        $settings = \App\Models\Parametre::all()->pluck('valeur', 'cle');

        // On parcourt tous les contrats actifs
        $contrats = Contrat::where('statut', 'actif')
            ->with(['locataire.user', 'bien', 'bien.proprietaire.user'])
            ->get();

        $count = 0;

        foreach ($contrats as $contrat) {
            $locataireUser = $contrat->locataire->user ?? null;
            if (!$locataireUser) continue;

            // Calcul de l'échéance mensuelle (J-5)
            $targetDay = min($contrat->jour_echeance, $today->daysInMonth);
            $echeanceThisMois = Carbon::create($today->year, $today->month, $targetDay);
            if ($echeanceThisMois->isPast()) $echeanceThisMois->addMonth();

            // CAS 1 : RAPPEL MENSUEL CLASSIQUE (J-5)
            if ($echeanceThisMois->isSameDay($reminderDate)) {
                $dejaPaye = Paiement::where('contrat_id', $contrat->id)
                    ->whereYear('mois_concerne', $echeanceThisMois->year)
                    ->whereMonth('mois_concerne', $echeanceThisMois->month)
                    ->where('statut', 'paye')
                    ->exists();

                if (!$dejaPaye) {
                    $locataireUser->notify(new \App\Notifications\PaymentReminderNotification($contrat, $contrat->loyer));
                    $this->info("  ✓ Rappel mensuel envoyé à {$locataireUser->name}");
                    $count++;
                }
            }

            // CAS 2 : FIN DE PAIEMENT ANNUEL (15 jours avant la fin des 12 mois payés)
            $dernierPaiement = Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'paye')
                ->orderBy('mois_concerne', 'desc')
                ->first();

            if ($dernierPaiement && $dernierPaiement->mois_concerne) {
                $finPeriode = Carbon::parse($dernierPaiement->mois_concerne)->endOfMonth();
                if ($finPeriode->diffInDays($today) == 15 && $finPeriode->isFuture()) {
                    // C'est le moment de prévenir pour la fin de l'année payée
                    $locataireUser->notify(new \App\Notifications\PaymentReminderNotification($contrat, $contrat->loyer, true));
                    $this->info("  ⚠ Alerte fin de paiement annuel envoyée à {$locataireUser->name}");
                    $count++;
                }
            }
        }

        $this->info("━━━ Fin du traitement : {$count} alertes déclenchées. ━━━");
    }
}
