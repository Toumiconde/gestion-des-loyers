<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use Carbon\Carbon;

class MarkLatePayments extends Command
{
    /**
     * Signature de la commande.
     */
    protected $signature = 'app:mark-late-payments';

    /**
     * Description de la commande.
     */
    protected $description = 'Marque automatiquement les paiements en retard et notifie les parties concernées.';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $this->info('━━━ Détection des paiements en retard ━━━');

        $today = Carbon::today();

        // On récupère tous les contrats actifs
        $contrats = Contrat::where('statut', 'actif')
            ->with(['locataire.user', 'bien', 'bien.proprietaire.user'])
            ->get();

        $countRetard    = 0;
        $countNotif     = 0;
        $countDejaMarque = 0;

        foreach ($contrats as $contrat) {
            $locataireUser    = $contrat->locataire->user ?? null;
            $proprietaireUser = $contrat->bien->proprietaire->user ?? null;

            if (!$locataireUser || !$proprietaireUser) continue;

            // L'échéance du mois courant (gestion des mois courts, ex: 31 -> 28/29 fév)
            $daysInMonth = $today->daysInMonth;
            $targetDay = min($contrat->jour_echeance, $daysInMonth);
            $echeanceMoisCourant = Carbon::create($today->year, $today->month, $targetDay);

            // On ne vérifie que si l'échéance du mois courant est DÉJÀ PASSÉE
            if (!$echeanceMoisCourant->isPast()) continue;

            // Chercher le paiement de ce mois
            $paiement = Paiement::where('contrat_id', $contrat->id)
                ->whereYear('mois_concerne', $echeanceMoisCourant->year)
                ->whereMonth('mois_concerne', $echeanceMoisCourant->month)
                ->first();

            if ($paiement) {
                // Un paiement existe — vérifier s'il est partiel et l'échéance est passée
                if ($paiement->statut === 'partiel') {
                    $paiement->update(['statut' => 'en_retard']);
                    $this->warn("  ⚠ Paiement PARTIEL → EN RETARD : {$locataireUser->name} ({$contrat->numero_contrat})");
                    $countRetard++;

                    // Notifier le locataire
                    $this->notifyRetard($contrat, $locataireUser, $proprietaireUser, $echeanceMoisCourant, $paiement->montant);
                    $countNotif++;
                } elseif ($paiement->statut === 'en_retard') {
                    $countDejaMarque++;
                }
                // Si 'paye' → tout va bien, on ne fait rien
            } else {
                // Aucun paiement ce mois → on crée un enregistrement "en_retard"
                Paiement::create([
                    'contrat_id'     => $contrat->id,
                    'mois_concerne'  => $echeanceMoisCourant->format('Y-m-d'),
                    'montant'        => 0,
                    'date_paiement'  => $today->format('Y-m-d'),
                    'mode_reglement' => 'autre',
                    'statut'         => 'en_retard',
                    'notes'          => '[Généré automatiquement] Loyer non reçu après la date d\'échéance.',
                    'created_by'     => User::where('role', 'admin')->value('id'),
                ]);

                $this->error("  ✗ Loyer ABSENT → EN RETARD créé : {$locataireUser->name} ({$contrat->numero_contrat})");
                $countRetard++;

                // Notifier le locataire et le propriétaire
                $this->notifyRetard($contrat, $locataireUser, $proprietaireUser, $echeanceMoisCourant, 0);
                $countNotif++;
            }
        }

        $this->info("━━━ Résumé : {$countRetard} retard(s) détecté(s) | {$countNotif} notification(s) | {$countDejaMarque} déjà marqué(s). ━━━");
    }

    /**
     * Notifie le locataire et le propriétaire d'un retard de paiement.
     */
    private function notifyRetard(Contrat $contrat, $locataireUser, $proprietaireUser, Carbon $echeance, float $montantVerse): void
    {
        $loyer          = $contrat->loyer;
        $reste          = $loyer - $montantVerse;
        $echeanceStr    = $echeance->format('d/m/Y');

        // Message au locataire
        $contentLocataire = "🚨 RETARD DE PAIEMENT\n\n"
            . "Bonjour {$locataireUser->name},\n\n"
            . "Votre loyer du mois de {$echeance->locale('fr')->isoFormat('MMMM YYYY')} "
            . "pour le bien « {$contrat->bien->libelle} » n'a pas été reçu ou est incomplet.\n\n"
            . "• Loyer attendu : " . number_format($loyer, 0, ',', ' ') . " GNF\n"
            . "• Montant reçu : " . number_format($montantVerse, 0, ',', ' ') . " GNF\n"
            . "• Reste à payer : " . number_format($reste, 0, ',', ' ') . " GNF\n\n"
            . "Veuillez régulariser votre situation dès que possible. Des pénalités peuvent s'appliquer.";

        Message::create([
            'sender_id'   => $proprietaireUser->id,
            'receiver_id' => $locataireUser->id,
            'content'     => $contentLocataire,
            'is_urgent'   => true,
            'type'        => 'urgent',
        ]);

        $locataireUser->notify(new ProfileUpdated(
            $proprietaireUser,
            "🚨 URGENT : Retard de paiement détecté pour {$echeance->locale('fr')->isoFormat('MMMM YYYY')} — " . number_format($reste, 0, ',', ' ') . " GNF restants."
        ));

        // Message au propriétaire
        $contentProprio = "📋 Notification de Retard\n\n"
            . "Votre locataire {$locataireUser->name} n'a pas réglé son loyer "
            . "du mois de {$echeance->locale('fr')->isoFormat('MMMM YYYY')} "
            . "(Bien : {$contrat->bien->libelle}).\n\n"
            . "• Montant dû : " . number_format($loyer, 0, ',', ' ') . " GNF\n"
            . "• Montant reçu : " . number_format($montantVerse, 0, ',', ' ') . " GNF\n"
            . "• Reste : " . number_format($reste, 0, ',', ' ') . " GNF\n\n"
            . "Un message de relance a été envoyé automatiquement au locataire.";

        // Trouver un admin pour envoyer au proprio
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            Message::create([
                'sender_id'   => $adminUser->id,
                'receiver_id' => $proprietaireUser->id,
                'content'     => $contentProprio,
                'is_urgent'   => true,
                'type'        => 'urgent',
            ]);

            $proprietaireUser->notify(new ProfileUpdated(
                $adminUser,
                "📋 Retard de loyer : {$locataireUser->name} n'a pas payé pour {$echeance->locale('fr')->isoFormat('MMMM YYYY')}."
            ));
        }
    }
}
