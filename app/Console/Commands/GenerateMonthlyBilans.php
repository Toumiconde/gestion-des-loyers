<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proprietaire;
use App\Models\Paiement;
use App\Models\Incident;
use App\Models\Bilan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyBilans extends Command
{
    protected $signature = 'bilans:generate {month?} {year?}';
    protected $description = 'Génère automatiquement les bilans mensuels pour tous les propriétaires';

    public function handle()
    {
        $month = $this->argument('month') ?: Carbon::now()->subMonth()->month;
        $year = $this->argument('year') ?: Carbon::now()->subMonth()->year;

        $this->info("Génération des bilans pour $month/$year...");

        $proprietaires = Proprietaire::all();

        foreach ($proprietaires as $p) {
            // 1. Calcul des revenus (Paiements validés)
            $totalRevenus = Paiement::whereHas('contrat.bien', function($q) use ($p) {
                $q->where('proprietaire_id', $p->id);
            })
            ->whereYear('mois_concerne', $year)
            ->whereMonth('mois_concerne', $month)
            ->where('statut', 'paye')
            ->sum('montant');

            // 2. Calcul des dépenses (Incidents payés)
            $totalDepenses = Incident::whereHas('contrat.bien', function($q) use ($p) {
                $q->where('proprietaire_id', $p->id);
            })
            ->where('statut', 'paye')
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->sum('cout_reel');

            // 3. Calcul de la commission agence
            $fraisGestion = ($totalRevenus * $p->commission_rate) / 100;
            $net = $totalRevenus - $totalDepenses - $fraisGestion;

            // 4. Création/Mise à jour du bilan
            Bilan::updateOrCreate(
                [
                    'proprietaire_id' => $p->id,
                    'mois' => $month,
                    'annee' => $year,
                ],
                [
                    'total_revenus' => $totalRevenus,
                    'total_depenses' => $totalDepenses,
                    'frais_gestion'  => $fraisGestion,
                    'montant_net' => $net,
                    'envoye_le' => now(), // On marque comme "envoyé" dès la génération automatique
                ]
            );

            $this->line("Bilan généré pour {$p->user->name} : Net $net GNF");
        }

        $this->info("Tous les bilans ont été générés avec succès.");
    }
}
