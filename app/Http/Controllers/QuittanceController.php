<?php

namespace App\Http\Controllers;

use App\Models\Quittance;
use App\Models\Paiement;
use Illuminate\Http\Request;

class QuittanceController extends Controller
{
    /**
     * Génère ou affiche la quittance d'un paiement.
     */
    public function generate(Paiement $paiement)
    {
        $quittance = $paiement->quittance;

        if (!$quittance) {
            // Si le paiement est payé mais n'a pas de quittance (cas importé ou bug), on la crée
            if ($paiement->statut === 'paye') {
                $count = Quittance::count() + 1;
                $quittance = Quittance::create([
                    'paiement_id'      => $paiement->id,
                    'numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year,
                ]);
            } else {
                return back()->with('error', 'Impossible de générer une quittance pour un paiement non soldé.');
            }
        }

        return redirect()->route('quittances.show', $quittance);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');
        
        $query = Quittance::with('paiement.contrat.locataire', 'paiement.contrat.bien');

        // Filtrage par date
        $query->whereHas('paiement', function($q) use ($selectedYear, $selectedMonth) {
            $q->whereYear('mois_concerne', $selectedYear);
            if ($selectedMonth) {
                $q->whereMonth('mois_concerne', $selectedMonth);
            }
        });

        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire) {
            $query->whereHas('paiement.contrat.bien', function($q) {
                $q->where('proprietaire_id', auth()->user()->proprietaire->id);
            });
        } elseif (auth()->user()->isLocataire() && auth()->user()->locataire) {
            $query->whereHas('paiement.contrat', function($q) {
                $q->where('locataire_id', auth()->user()->locataire->id);
            });
        }

        $quittances = $query->latest()->paginate(10);
        $years = range(2024, now()->year + 1);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('quittances.index', compact('quittances', 'years', 'months'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->isProprietaire() || auth()->user()->isLocataire()) {
            abort(403, 'Accès non autorisé.');
        }
        return view('quittances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(403, 'La création manuelle de quittance est désactivée.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quittance $quittance, Request $request)
    {
        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire) {
            if ($quittance->paiement->contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
                abort(403, 'Cette quittance ne vous concerne pas.');
            }
        } elseif (auth()->user()->isLocataire() && auth()->user()->locataire) {
            if ($quittance->paiement->contrat->locataire_id !== auth()->user()->locataire->id) {
                abort(403, 'Cette quittance ne vous concerne pas.');
            }
        }

        $quittance->load('paiement.contrat.locataire', 'paiement.contrat.bien.proprietaire.user');

        if ($request->has('print')) {
            return view('quittances.print', compact('quittance'));
        }

        return view('quittances.show', compact('quittance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quittance $quittance)
    {
        return view('quittances.edit', compact('quittance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quittance $quittance)
    {
        abort(403, 'Une quittance ne peut pas être modifiée.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quittance $quittance)
    {
        abort(403, 'Une quittance ne peut pas être supprimée.');
    }
}
