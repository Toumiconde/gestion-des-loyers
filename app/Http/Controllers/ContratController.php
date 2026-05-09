<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Bien;
use App\Models\Locataire;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    private function authorizeEditor()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isProprietaire()) {
            abort(403, 'Accès non autorisé - Seuls les administrateurs et propriétaires peuvent gérer les contrats.');
        }
    }

    public function index(Request $request)
    {
        $selectedYear = $request->get('year', session('selected_year', date('Y')));
        $selectedMonth = $request->get('month');

        $query = Contrat::with('bien', 'locataire')
                        ->whereYear('date_debut', $selectedYear);

        if (auth()->user()->isAdmin() && $selectedMonth) {
            $query->whereMonth('date_debut', $selectedMonth);
        }

        if (auth()->user()->isProprietaire()) {
            if (!auth()->user()->proprietaire) {
                abort(403, 'Profil propriétaire non configuré.');
            }
            $proprietaireId = auth()->user()->proprietaire->id;
            $query->whereHas('bien', function($q) use ($proprietaireId) {
                $q->where('proprietaire_id', $proprietaireId);
            });
        } elseif (auth()->user()->isLocataire()) {
            $locataireId = auth()->user()->locataire->id ?? 0;
            $query->where('locataire_id', $locataireId);
        }

        $contrats = $query->paginate(15);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('contrats.index', compact('contrats', 'selectedYear', 'selectedMonth', 'months'));
    }

    public function create()
    {
        $this->authorizeEditor();
        $user = auth()->user();

        $query = Bien::where('statut', 'disponible');

        if ($user->isProprietaire()) {
            $query->where('proprietaire_id', $user->proprietaire->id);
        }

        $biens = $query->get();
        $locataires = Locataire::all();
        
        return view('contrats.create', compact('biens', 'locataires'));
    }

    public function store(Request $request)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'bien_id'         => 'required|exists:biens,id',
            'locataire_id'    => 'required|exists:locataires,id',
            'date_debut'      => 'required|date',
            'duree_mois'      => 'nullable|integer|min:1',
            'loyer'           => 'required|numeric|min:1',
            'depot_garantie'  => 'required|numeric|min:0',
            'jour_echeance'   => 'required|integer|min:1|max:28',
            'taux_revision'   => 'nullable|numeric|min:0|max:10',
        ]);

        if ($validated['duree_mois']) {
            $validated['date_fin'] = now()->parse($validated['date_debut'])
                                         ->addMonths((int) $validated['duree_mois'])
                                         ->subDay();
        }

        $count = Contrat::whereYear('created_at', now()->year)->count() + 1;
        $validated['numero_contrat'] = 'C' . str_pad($count, 3, '0', STR_PAD_LEFT) . '-' . now()->year;

        $contrat = Contrat::create($validated);
        $contrat->bien->update(['statut' => 'occupe']);

        // Notification Admin : Attribution du bien
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ProfileUpdated(auth()->user(), "🔑 Nouvelle Attribution : " . $contrat->bien->libelle . " a été attribué à " . $contrat->locataire->nom));

        return redirect()->route('contrats.index')
                         ->with('success', 'Contrat créé avec succès. Numéro : ' . $validated['numero_contrat']);
    }

    public function show(Contrat $contrat)
    {
        // ... (conservé identique)
        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire) {
            if ($contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
                abort(403, 'Ce contrat ne vous concerne pas.');
            }
        } elseif (auth()->user()->isLocataire() && auth()->user()->locataire) {
            if ($contrat->locataire_id !== auth()->user()->locataire->id) {
                abort(403, 'Ce contrat ne vous concerne pas.');
            }
        }

        $contrat->load('bien.proprietaire.user', 'locataire', 'paiements.quittance', 'incidents', 'relances');
        return view('contrats.show', compact('contrat'));
    }

    public function edit(Contrat $contrat)
    {
        $this->authorizeEditor();
        return view('contrats.edit', compact('contrat'));
    }

    public function update(Request $request, Contrat $contrat)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'loyer'          => 'required|numeric|min:1',
            'depot_garantie' => 'required|numeric|min:0',
            'jour_echeance'  => 'required|integer|min:1|max:28',
            'taux_revision'  => 'nullable|numeric|min:0|max:10',
        ]);

        $contrat->update($validated);

        return redirect()->route('contrats.show', $contrat)
                         ->with('success', 'Contrat mis à jour.');
    }

    public function destroy(Contrat $contrat)
    {
        // Autoriser Admin OU Propriétaire du bien
        if (!auth()->user()->isAdmin() && (!auth()->user()->isProprietaire() || $contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id)) {
            abort(403, 'Vous n\'êtes pas autorisé à résilier ce contrat.');
        }

        // VÉRIFICATION DES DETTES
        $dettesCount = $contrat->paiements()->where('statut', '!=', 'paye')->count();
        if ($dettesCount > 0) {
            return back()->with('error', '🚨 Impossible de libérer le locataire : il possède encore ' . $dettesCount . ' paiement(s) non régularisé(s). Tout doit être réglé avant le départ.');
        }

        $data = request()->validate([
            'motif_resiliation' => 'required|in:depart_volontaire,non_paiement,fin_bail,autre',
        ]);

        $contrat->update([
            'statut'            => 'resilie',
            'motif_resiliation' => $data['motif_resiliation'],
            'date_resiliation'  => now(),
        ]);

        // Remettre le bien en "Prêt et Vide"
        $contrat->bien->update(['statut' => 'disponible']);

        // Notification Admin : Libération du bien
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ProfileUpdated(auth()->user(), "🏠 Location libérée : " . $contrat->locataire->nom . " a quitté le bien " . $contrat->bien->libelle . " à " . $contrat->bien->ville));

        return redirect()->route('contrats.index')
                         ->with('success', 'Contrat résilié. Le locataire a été libéré et le bien est marqué comme Vide et Prêt.');
    }
}