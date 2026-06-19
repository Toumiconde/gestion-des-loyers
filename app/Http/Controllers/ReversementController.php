<?php

namespace App\Http\Controllers;

use App\Models\Bilan;
use App\Models\Proprietaire;
use App\Models\Paiement;
use App\Models\Incident;
use Illuminate\Http\Request;

class ReversementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Bilan::with('proprietaire.user')->latest('annee')->latest('mois');

        if ($user->role === 'proprietaire') {
            $query->where('proprietaire_id', $user->proprietaire->id);
        }

        $reversements = $query->paginate(15);

        return view('reversements.index', compact('reversements'));
    }

    public function show(Bilan $bilan)
    {
        // Vérification des accès
        $user = auth()->user();
        if ($user->role === 'proprietaire' && $bilan->proprietaire_id !== $user->proprietaire->id) {
            abort(403);
        }

        $bilan->load('proprietaire.user');

        // Récupérer le détail des loyers du mois
        $detailsLoyers = Paiement::whereYear('mois_concerne', $bilan->annee)
            ->whereMonth('mois_concerne', $bilan->mois)
            ->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $bilan->proprietaire_id))
            ->where('statut', 'paye')
            ->with('contrat.locataire', 'contrat.bien')
            ->get();

        // Récupérer le détail des dépenses (incidents) du mois
        $detailsDepenses = Incident::whereYear('updated_at', $bilan->annee)
            ->whereMonth('updated_at', $bilan->mois)
            ->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $bilan->proprietaire_id))
            ->where('statut', 'paye')
            ->with('contrat.bien')
            ->get();

        return view('reports.reversement', compact('bilan', 'detailsLoyers', 'detailsDepenses'));
    }

    public function markAsPaid(Request $request, Bilan $bilan)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isComptable()) {
            abort(403);
        }

        $validated = $request->validate([
            'date_virement' => 'required|date',
            'mode_paiement' => 'required|string',
            'ref_virement' => 'nullable|string|max:100',
        ]);

        $bilan->update([
            'statut' => 'virement_effectue',
            'date_virement' => $validated['date_virement'],
            'mode_paiement' => $validated['mode_paiement'],
            'ref_virement' => $validated['ref_virement'],
        ]);

        // Notification au propriétaire
        if ($bilan->proprietaire->user) {
            $bilan->proprietaire->user->notify(new \App\Notifications\OwnerPayoutConfirmedNotification($bilan));
        }

        \App\Models\ActivityLog::log('paiement', "Reversement effectué pour {$bilan->proprietaire->user->name} - Montant: " . number_format($bilan->montant_net, 0, ',', ' ') . " GNF", $bilan);

        return back()->with('success', 'Versement enregistré avec succès.');
    }
}
