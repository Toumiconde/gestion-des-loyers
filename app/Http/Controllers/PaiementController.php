<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Contrat;
use App\Models\Quittance;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index()
    {
        $paiements = Paiement::with('contrat.locataire', 'contrat.bien')->paginate(10);
        return view('paiements.index', compact('paiements'));
    }

    public function create()
    {
        $contrats = Contrat::where('statut', 'actif')->with('locataire', 'bien')->get();
        return view('paiements.create', compact('contrats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrat_id'     => 'required|exists:contrats,id',
            'mois_concerne'  => 'required|date',
            'montant'        => 'required|numeric|min:1',
            'date_paiement'  => 'required|date',
            'mode_reglement' => 'required|in:especes,virement,mobile_money,cheque,autre',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        $contrat = Contrat::find($validated['contrat_id']);

        // Calcul automatique du statut selon le montant
        if ($validated['montant'] >= $contrat->loyer) {
            $validated['statut'] = 'paye';
        } else {
            $validated['statut'] = 'partiel';
        }

        $validated['created_by'] = auth()->id();

        $paiement = Paiement::create($validated);

        // Génération automatique de la quittance si paiement complet
        if ($paiement->statut === 'paye') {
            $count = Quittance::count() + 1;
            Quittance::create([
                'paiement_id'      => $paiement->id,
                'numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year,
            ]);
        }

        return redirect()->route('paiements.index')
                         ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(Paiement $paiement)
    {
        $paiement->load('contrat.locataire', 'contrat.bien', 'quittance');
        return view('paiements.show', compact('paiement'));
    }

    // On ne modifie pas un paiement (règle RP-05)
    public function edit(Paiement $paiement)
    {
        abort(403, 'Un paiement enregistré ne peut pas être modifié.');
    }

    public function update(Request $request, Paiement $paiement)
    {
        abort(403, 'Un paiement enregistré ne peut pas être modifié.');
    }

    public function destroy(Paiement $paiement)
    {
        abort(403, 'Un paiement ne peut pas être supprimé.');
    }
}