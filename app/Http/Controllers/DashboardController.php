<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Incident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales pour le tableau de bord
        $stats = [
            // Nombre total de biens
            'total_biens'        => Bien::count(),
            // Biens occupés en ce moment
            'biens_occupes'      => Bien::where('statut', 'occupe')->count(),
            // Biens disponibles
            'biens_disponibles'  => Bien::where('statut', 'disponible')->count(),
            // Contrats actifs
            'contrats_actifs'    => Contrat::where('statut', 'actif')->count(),
            // Paiements du mois en cours
            'paiements_ce_mois'  => Paiement::whereMonth('date_paiement', now()->month)
                                            ->whereYear('date_paiement', now()->year)
                                            ->count(),
            // Incidents ouverts non résolus
            'incidents_ouverts'  => Incident::where('statut', 'ouvert')->count(),
            // Loyers en retard
            'loyers_en_retard'   => Paiement::where('statut', 'en_retard')->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}