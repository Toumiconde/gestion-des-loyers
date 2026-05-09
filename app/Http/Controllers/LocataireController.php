<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\Request;

class LocataireController extends Controller
{
    private function authorizeEditor()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isProprietaire()) {
            abort(403, 'Accès non autorisé - Seuls les administrateurs et propriétaires peuvent gérer les locataires.');
        }
    }

    public function index(Request $request)
    {
        $selectedYear = $request->get('year', session('selected_year', date('Y')));
        $selectedMonth = $request->get('month'); // Filtre exclusif Admin
        
        $endOfYear = \Carbon\Carbon::create($selectedYear, 12, 31)->endOfDay();

        $query = Locataire::with('contratActif.bien')
                    ->withTrashed()
                    ->whereYear('created_at', $selectedYear);

        // FILTRE MOIS EXCLUSIF ADMIN
        if (auth()->user()->isAdmin() && $selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if (auth()->user()->isProprietaire()) {
            if (!auth()->user()->proprietaire) {
                abort(403, 'Profil propriétaire non configuré.');
            }
            $proprietaireId = auth()->user()->proprietaire->id;
            $query->whereHas('contrats.bien', function($q) use ($proprietaireId) {
                $q->where('proprietaire_id', $proprietaireId);
            });
        } elseif (auth()->user()->isLocataire()) {
            // Un locataire ne voit que lui-même
            $locataireId = auth()->user()->locataire->id ?? 0;
            $query->where('id', $locataireId);
        }

        $locataires = $query->paginate(15);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Guin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('locataires.index', compact('locataires', 'selectedYear', 'selectedMonth', 'months'));
    }

    public function create()
    {
        $this->authorizeEditor();
        return view('locataires.create');
    }

    public function store(Request $request)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'email'           => 'nullable|email|max:191',
            'telephone'       => 'nullable|string|max:20',
            'adresse'         => 'nullable|string',
            'piece_identite'  => 'nullable|string|max:100',
        ]);

        Locataire::create($validated);

        return redirect()->route('locataires.index')
                         ->with('success', 'Locataire ajouté avec succès.');
    }

    public function show(Locataire $locataire)
    {
        if (auth()->user()->isProprietaire()) {
            if (!auth()->user()->proprietaire) {
                abort(403, 'Profil propriétaire non configuré.');
            }
            $isAuthorized = $locataire->contrats()->whereHas('bien', function($q) {
                $q->where('proprietaire_id', auth()->user()->proprietaire->id);
            })->exists();

            if (!$isAuthorized) {
                abort(403, 'Ce locataire n\'est pas lié à vos biens.');
            }
        }

        if (auth()->user()->isLocataire()) {
            if (!auth()->user()->locataire || $locataire->id !== auth()->user()->locataire->id) {
                abort(403, 'Vous ne pouvez pas consulter le profil d\'un autre locataire.');
            }
        }

        $locataire->load(['contrats.bien', 'contrats.paiements' => function($q) {
            $q->orderBy('mois_concerne', 'asc');
        }]);

        // CALCUL DU SCORE DE FIABILITÉ (ÉVOLUTION)
        $scoreHistory = [];
        $labels = [];
        $currentScore = 70; // On commence avec un capital confiance de 70%

        foreach ($locataire->contrats as $contrat) {
            foreach ($contrat->paiements as $p) {
                if ($p->statut === 'paye') {
                    $currentScore += 5;
                } elseif ($p->statut === 'en_retard') {
                    $currentScore -= 10;
                } elseif ($p->statut === 'impaye' || $p->statut === 'partiel') {
                    $currentScore -= 20;
                }
                
                // On cap entre 0 et 100
                $currentScore = max(0, min(100, $currentScore));
                
                $scoreHistory[] = $currentScore;
                $labels[] = \Carbon\Carbon::parse($p->mois_concerne)->locale('fr')->isoFormat('MMM YY');
            }
        }

        // Si pas d'historique, on met un point de départ
        if (empty($scoreHistory)) {
            $scoreHistory = [70];
            $labels = ['Début'];
        }

        $latestScore = end($scoreHistory);
        $previousScore = count($scoreHistory) > 1 ? $scoreHistory[count($scoreHistory)-2] : 70;
        $isDropping = $latestScore < $previousScore;

        return view('locataires.show', compact('locataire', 'scoreHistory', 'labels', 'latestScore', 'isDropping'));
    }

    public function edit(Locataire $locataire)
    {
        $this->authorizeEditor();
        return view('locataires.edit', compact('locataire'));
    }

    public function update(Request $request, Locataire $locataire)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'nullable|email|max:191',
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'nullable|string',
            'piece_identite' => 'nullable|string|max:100',
        ]);

        $locataire->update($validated);

        return redirect()->route('locataires.index')
                         ->with('success', 'Locataire mis à jour.');
    }

    public function destroy(Locataire $locataire)
    {
        $this->authorizeEditor();
        if ($locataire->contratActif) {
            return redirect()->route('locataires.index')
                             ->with('error', 'Impossible de supprimer un locataire avec un contrat actif.');
        }

        $locataire->delete();

        return redirect()->route('locataires.index')
                         ->with('success', 'Locataire supprimé.');
    }
}