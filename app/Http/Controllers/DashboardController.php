<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Incident;
use App\Models\Locataire;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupération des filtres avec persistance en session
        if ($request->has('year')) {
            session(['selected_year' => $request->get('year')]);
        }
        if ($request->has('month')) {
            session(['selected_month' => $request->get('month')]);
        }
        
        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');

        // Liste des années pour le filtre
        $years = range(2024, 2030);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $user = auth()->user();

        // Si le locataire n'a pas encore de logement, on le redirige vers la recherche
        if ($user->role === 'locataire' && (!$user->locataire || !$user->locataire->contrats()->where('statut', 'actif')->exists())) {
            $locataireId = $user->locataire->id ?? 0;
            // Vérifier s'il a déjà des demandes en cours
            $hasPendingRequests = \App\Models\DemandeLocation::where('user_id', $user->id)
                ->whereIn('statut', ['en_attente', 'valide_proprietaire', 'valide_admin', 'accepte'])
                ->exists();
            
            if (!$hasPendingRequests) {
                return redirect()->route('recherche.index')->with('info', 'Bienvenue ! Veuillez choisir un logement pour commencer.');
            }
        }

        $labels = [];
        $data = [];

        // --- FILTRAGE TEMPOREL ET PAR RÔLE ---
        $endOfYear = Carbon::create($selectedYear, 12, 31)->endOfDay();
        
        $queryPaiements = Paiement::withTrashed()->whereYear('paiements.mois_concerne', $selectedYear);
            
        $queryIncidents = Incident::withTrashed()->whereYear('incidents.created_at', $selectedYear);
            
        $queryBiens = Bien::withTrashed()->whereYear('biens.created_at', $selectedYear);
            
        $queryLocataires = Locataire::withTrashed()->whereYear('locataires.created_at', $selectedYear);
            
        $queryContrats = Contrat::withTrashed()->whereYear('contrats.created_at', $selectedYear);

        if ($user->role === 'locataire') {
            $locataireId = $user->locataire->id ?? 0;
            $queryPaiements->whereHas('contrat', fn($q) => $q->where('locataire_id', $locataireId));
            $queryIncidents->whereHas('contrat', fn($q) => $q->where('locataire_id', $locataireId));
            $queryBiens->whereHas('contrats', fn($q) => $q->where('locataire_id', $locataireId));
            $queryLocataires->where('id', $locataireId);
            $queryContrats->where('locataire_id', $locataireId);
        } elseif ($user->role === 'proprietaire') {
            $proprietaireId = $user->proprietaire->id ?? 0;
            $queryPaiements->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
            $queryIncidents->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
            $queryBiens->where('proprietaire_id', $proprietaireId);
            $queryLocataires->whereHas('contrats.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
            $queryContrats->whereHas('bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
        }

        // --- DONNÉES DU GRAPHIQUE DYNAMIQUE (ANNUEL OU MENSUEL) ---
        $dataCurrentYear = [];
        $dataPastYear = [];
        $labelsGraphe = [];
        $pastYear = $selectedYear - 1;

        if ($selectedMonth) {
            // VUE MENSUELLE (JOUR PAR JOUR)
            $daysInMonth = Carbon::create($selectedYear, $selectedMonth)->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labelsGraphe[] = "Jour " . $d;
                
                // Année en cours
                $dataCurrentYear[] = (clone $queryPaiements)
                                    ->whereYear('mois_concerne', $selectedYear)
                                    ->whereMonth('mois_concerne', $selectedMonth)
                                    ->whereDay('date_paiement', $d)
                                    ->where('statut', 'paye')
                                    ->sum('montant');
                
                // Année précédente (pour comparaison si besoin, sinon 0)
                $dataPastYear[] = (clone $queryPaiements)
                                    ->whereYear('mois_concerne', $pastYear)
                                    ->whereMonth('mois_concerne', $selectedMonth)
                                    ->whereDay('date_paiement', $d)
                                    ->where('statut', 'paye')
                                    ->sum('montant');
            }
        } else {
            // VUE ANNUELLE (MOIS PAR MOIS)
            $labelsGraphe = array_values($months);
            for ($m = 1; $m <= 12; $m++) {
                $dataCurrentYear[] = (clone $queryPaiements)->whereYear('mois_concerne', $selectedYear)
                                    ->whereMonth('mois_concerne', $m)
                                    ->where('statut', 'paye')
                                    ->sum('montant');
                
                $dataPastYear[] = (clone $queryPaiements)->whereYear('mois_concerne', $pastYear)
                                    ->whereMonth('mois_concerne', $m)
                                    ->where('statut', 'paye')
                                    ->sum('montant');
            }
        }

        // --- DONNÉES DU GRAPHIQUE CIRCULAIRE ---
        $bienStats = (clone $queryBiens)->select('statut', DB::raw('count(*) as count'))
                         ->groupBy('statut')
                         ->get();
        
        $statutLabels = $bienStats->pluck('statut')->map(fn($s) => ucfirst($s))->toArray();
        $statutCounts = $bienStats->pluck('count')->toArray();

        // --- STATISTIQUES FINANCIÈRES FILTRÉES PAR PÉRIODE ---
        $queryPaiementsPeriode = (clone $queryPaiements);
        if ($selectedMonth) {
            $queryPaiementsPeriode->whereMonth('paiements.mois_concerne', $selectedMonth);
        }

        $queryIncidentsPeriode = (clone $queryIncidents)->whereYear('incidents.created_at', $selectedYear);
        if ($selectedMonth) {
            $queryIncidentsPeriode->whereMonth('incidents.created_at', $selectedMonth);
        }

        // --- STATISTIQUES GLOBALES FILTRÉES PAR PÉRIODE ---
        $queryContratsPeriode = (clone $queryContrats)->where(function($q) use ($selectedYear, $selectedMonth) {
            $startDate = Carbon::create($selectedYear, $selectedMonth ?: 1, 1)->startOfMonth();
            $endDate = $selectedMonth 
                       ? Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
                       : Carbon::create($selectedYear, 12, 31)->endOfMonth();

            $q->where('date_debut', '<=', $endDate)
              ->where(function($sq) use ($startDate) {
                  $sq->whereNull('date_fin')
                     ->orWhere('date_fin', '>=', $startDate);
              });
        });

        // --- RÉSUMÉ DES DERNIÈRES 24H (POUR ADMIN) ---
        $dailySummary = [];
        $isCurrentYear = ($selectedYear == date('Y'));
        $isCurrentMonth = ($selectedMonth == date('n'));
        $showFlash = $isCurrentYear && (!$selectedMonth || $isCurrentMonth);
        
        if (in_array($user->role, ['admin', 'comptable', 'gestionnaire']) && $showFlash) {
            $last24h = Carbon::now()->subHours(24);
            $dailySummary = [
                'connexions' => ActivityLog::where('action', 'connexion')->where('created_at', '>=', $last24h)->count(),
                'paiements'  => Paiement::where('created_at', '>=', $last24h)->count(),
                'montant'    => Paiement::where('created_at', '>=', $last24h)->sum('montant'),
                'updates'    => ActivityLog::whereIn('action', ['creation', 'modification'])->where('created_at', '>=', $last24h)->count(),
                'top_actions' => ActivityLog::where('created_at', '>=', $last24h)
                                ->where('action', '!=', 'connexion')
                                ->with('user')
                                ->latest()
                                ->take(3)
                                ->get()
            ];
        }

        // --- FILTRAGE STRICT DES LOGS D'ACTIVITÉ ---
        $activityLogs = ActivityLog::query()
                                ->whereYear('created_at', $selectedYear)
                                ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
                                ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
                                ->with('user')
                                ->latest()
                                ->take(10)
                                ->get();

        // Compteur de tickets support (pour tout le monde sauf comptable)
        $supportTicketsCount = 0;
        if (auth()->user()->role !== 'comptable') {
            $supportTicketsCount = \App\Models\Message::where('is_support', true)
                ->where('is_read', false)
                ->where('receiver_id', $user->id)
                ->count();
        }

        $totalRevenus = (clone $queryPaiementsPeriode)->sum('montant');
        
        // DÉPENSES & BILAN NET
        if ($user->role === 'proprietaire') {
            $proprietaireId = $user->proprietaire->id ?? 0;
            $totalDepenses = Incident::whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId))
                ->where('statut', 'paye')
                ->whereYear('updated_at', $selectedYear)
                ->when($selectedMonth, fn($q) => $q->whereMonth('updated_at', $selectedMonth))
                ->sum('cout_reel');
        } else {
            $depensesQuery = \App\Models\Depense::whereYear('date_depense', $selectedYear);
            if ($selectedMonth) {
                $depensesQuery->whereMonth('date_depense', $selectedMonth);
            }
            $totalDepenses = $depensesQuery->sum('montant');
        }
        
        $beneficeNet = $totalRevenus - $totalDepenses;
        if ($user->role === 'proprietaire' && $user->proprietaire) {
            $commission = ($totalRevenus * ($user->proprietaire->commission_rate ?? 10)) / 100;
            $beneficeNet -= $commission;
        }

        // --- CALCULS COMPTABLES SPÉCIFIQUES ---
        // Détails des commissions agence (Somme des frais de gestion des bilans)
        $totalCommissions = \App\Models\Bilan::whereYear('annee', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('mois', $selectedMonth))
            ->sum('frais_gestion');

        // Liste détaillée des retards de paiement (Balance Âgée)
        $detailsRetards = (clone $queryPaiementsPeriode)
            ->where('statut', 'en_retard')
            ->with('contrat.locataire', 'contrat.bien')
            ->get();

        $totalLoyers = (clone $queryContratsPeriode)->sum('loyer');
        $totalCharges = (clone $queryContratsPeriode)->join('biens', 'contrats.bien_id', '=', 'biens.id')->sum('biens.charges');
        $revenuNet = $totalRevenus - $totalDepenses; 
        
        $totalBiensPotentiels = (clone $queryBiens)->count();
        $tauxOccupation = $totalBiensPotentiels > 0 
            ? ((clone $queryLocataires)->whereHas('contrats', fn($q) => $q->where('statut', 'actif'))->count() / $totalBiensPotentiels) * 100 
            : 0;

        $stats = [
            'selected_year'  => $selectedYear,
            'selected_month' => $selectedMonth,
            'years'          => $years,
            'months'         => $months,
            'biens_count'    => $queryBiens->count(),
            'locataires_count' => $queryLocataires->count(),
            'contrats_count' => (clone $queryLocataires)->whereHas('contrats', fn($q) => $q->where('statut', 'actif'))->count(),
            'total_loyers'   => $totalLoyers,
            'total_charges'  => $totalCharges,
            'revenu_net'     => $revenuNet,
            'taux_occupation' => $tauxOccupation,
            'activity_logs'   => $activityLogs,
            'locataires_liste' => (clone $queryLocataires)->with('contratActif.bien')->latest()->take(5)->get(),
            'recent_proprietaires' => in_array($user->role, ['admin', 'gestionnaire', 'comptable']) 
                                        ? \App\Models\Proprietaire::with('user')->latest()->take(5)->get()
                                        : collect(),
            'bilans_officiels' => ($user->role === 'proprietaire' && $user->proprietaire) 
                                    ? \App\Models\Bilan::where('proprietaire_id', $user->proprietaire->id)
                                        ->where('annee', $selectedYear)
                                        ->get()
                                        ->keyBy('mois')
                                    : collect(),
            
            'derniers_paiements' => (clone $queryPaiementsPeriode)->with('contrat.locataire', 'contrat.bien')
                                             ->latest()
                                             ->take(10)
                                             ->get(),

            'labels_mois'    => $labelsGraphe,
            'data_paiements' => $dataCurrentYear,
            'data_past_year' => $dataPastYear,
            'statut_labels'  => $statutLabels,
            'statut_counts'  => $statutCounts,
            'daily_summary'  => $dailySummary,
            'period' => [
                'revenus'           => $totalRevenus,
                'total_depenses'    => $totalDepenses,
                'benefice_net'      => $beneficeNet,
                'paiements_count'   => (clone $queryPaiementsPeriode)->count(),
                'incidents_ouverts' => (clone $queryIncidentsPeriode)->count(),
                'loyers_en_retard'  => (clone $queryPaiementsPeriode)->where('statut', 'en_retard')->count(),
                'commission_net'    => $totalCommissions,
            ],
            'global' => [
                'total_biens'      => (clone $queryBiens)->count(),
                'total_locataires' => (clone $queryLocataires)->count(),
                'total_contrats'   => (clone $queryContratsPeriode)->count(),
            ],
            'support_tickets_count' => $supportTicketsCount,
            'recent_support_requests' => (auth()->user()->role === 'comptable') 
                ? collect()
                : \App\Models\Message::where('is_support', true)
                    ->where(function($q) use ($user) {
                        if (in_array($user->role, ['admin', 'gestionnaire'])) return; // Le staff voit tout
                        $q->where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id);
                    })
                    ->with('sender', 'receiver')
                    ->latest()
                    ->take(5)
                    ->get(),
            'profile_updates' => $activityLogs->where('action', 'profile_updated')->take(5),
            'details_retards' => $detailsRetards,
            'recent_transactions' => Paiement::with('contrat.locataire')->latest()->take(5)->get(),
            'recent_expenses' => \App\Models\Depense::latest()->take(5)->get(),
            'bilans_a_payer'  => in_array($user->role, ['admin', 'comptable'])
                                    ? \App\Models\Bilan::where('statut', 'cloture')
                                        ->with('proprietaire.user')
                                        ->latest()
                                        ->get()
                                    : collect(),
        ];

        // LOGIQUE SPÉCIFIQUE LOCATAIRE
        if ($user->role === 'locataire' && $user->locataire) {
            $locataire = $user->locataire;
            $contrat = $locataire->contrats()->where('statut', 'actif')->latest()->first();
            
            if ($contrat) {
                $stats['locataire_data'] = [
                    'contrat' => $contrat,
                    'bien' => $contrat->bien,
                    'bail_doc' => \App\Models\Document::where('documentable_id', $contrat->id)
                                    ->where('documentable_type', 'App\Models\Contrat')
                                    ->where('type', 'bail')
                                    ->first(),
                    'incidents' => $contrat->incidents()->latest()->take(5)->get()
                ];
            }
        }

        return view('dashboard', compact('stats'));
    }

    public function exportMonthly(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));
        $monthNum = $request->get('month', date('n'));
        $user = auth()->user();

        $revenusQuery = \App\Models\Paiement::whereYear('mois_concerne', $selectedYear)
            ->whereMonth('mois_concerne', $monthNum)
            ->with('contrat.locataire', 'contrat.bien');

        if ($user->role === 'proprietaire' && $user->proprietaire) {
            $proprietaireId = $user->proprietaire->id;
            $revenusQuery->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
            
            $depenses = \App\Models\Incident::whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId))
                ->where('statut', 'paye')
                ->whereYear('updated_at', $selectedYear)
                ->whereMonth('updated_at', $monthNum)
                ->get()
                ->map(function($incident) {
                    return (object)[
                        'libelle' => 'Maintenance : ' . $incident->objet,
                        'categorie' => 'maintenance',
                        'montant' => $incident->cout_reel,
                        'date_depense' => $incident->updated_at
                    ];
                });
        } else {
            $depenses = \App\Models\Depense::whereYear('date_depense', $selectedYear)
                ->whereMonth('date_depense', $monthNum)
                ->get();
        }
            
        $revenus = $revenusQuery->get();
        
        // Calcul commission si propriétaire
        $fraisGestion = 0;
        if ($user->role === 'proprietaire' && $user->proprietaire) {
            $bilan = \App\Models\Bilan::where('proprietaire_id', $user->proprietaire->id)
                ->where('annee', $selectedYear)
                ->where('mois', $monthNum)
                ->first();
            
            if ($bilan) {
                $fraisGestion = $bilan->frais_gestion;
            } else {
                $fraisGestion = ($revenus->sum('montant') * ($user->proprietaire->commission_rate ?? 10)) / 100;
            }
        }

        return view('reports.monthly', compact('revenus', 'depenses', 'selectedYear', 'monthNum', 'fraisGestion'));
    }

    public function cloturer(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire() && !auth()->user()->isComptable()) {
            abort(403);
        }

        $request->validate([
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'year'            => 'required|integer',
            'month'           => 'required|integer',
        ]);

        $prop = \App\Models\Proprietaire::findOrFail($request->proprietaire_id);
        $year = $request->year;
        $month = $request->month;

        // Calcul des montants
        $revenus = \App\Models\Paiement::whereYear('mois_concerne', $year)
            ->whereMonth('mois_concerne', $month)
            ->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $prop->id))
            ->where('statut', 'paye')
            ->sum('montant');

        $depenses = \App\Models\Incident::whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $prop->id))
            ->where('statut', 'paye')
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->sum('cout_reel');

        $commissionRate = $prop->commission_rate ?? 10;
        $fraisGestion = ($revenus * $commissionRate) / 100;
        $net = $revenus - $depenses - $fraisGestion;

        \App\Models\Bilan::updateOrCreate(
            [
                'proprietaire_id' => $prop->id,
                'annee'           => $year,
                'mois'            => $month,
            ],
            [
                'total_revenus'  => $revenus,
                'total_depenses' => $depenses,
                'frais_gestion'  => $fraisGestion,
                'montant_net'    => $net,
                'statut'         => 'cloture',
            ]
        );

        return back()->with('success', "Le mois de " . $month . "/" . $year . " a été clôturé pour " . $prop->user->name);
    }
}