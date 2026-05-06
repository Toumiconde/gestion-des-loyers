<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Bien;
use App\Models\Locataire;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    public function index()
    {
        $contrats = Contrat::with('bien', 'locataire')->paginate(10);
        return view('contrats.index', compact('contrats'));
    }

    public function create()
    {
        // Seulement les biens disponibles
        $biens      = Bien::where('statut', 'disponible')->get();
        $locataires = Locataire::all();
        return view('contrats.create', compact('biens', 'locataires'));
    }

    public function store(Request $request)
    {
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

        // Calcul automatique de la date de fin
        if ($validated['duree_mois']) {
            $validated['date_fin'] = now()->parse($validated['date_debut'])
                                         ->addMonths($validated['duree_mois'])
                                         ->subDay();
        }

        // Génération du numéro de contrat unique ex: C001-2026
        $count = Contrat::whereYear('created_at', now()->year)->count() + 1;
        $validated['numero_contrat'] = 'C' . str_pad($count, 3, '0', STR_PAD_LEFT) . '-' . now()->year;

        // Création du contrat
        $contrat = Contrat::create($validated);

        // Le bien passe automatiquement en "occupé"
        $contrat->bien->update(['statut' => 'occupe']);

        return redirect()->route('contrats.index')
                         ->with('success', 'Contrat créé avec succès. Numéro : ' . $validated['numero_contrat']);
    }

    public function show(Contrat $contrat)
    {
        $contrat->load('bien.proprietaire.user', 'locataire', 'paiements.quittance', 'incidents', 'relances');
        return view('contrats.show', compact('contrat'));
    }

    public function edit(Contrat $contrat)
    {
        return view('contrats.edit', compact('contrat'));
    }

    public function update(Request $request, Contrat $contrat)
    {
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

    // Résiliation d'un contrat
    public function destroy(Contrat $contrat)
    {
        $request = request()->validate([
            'motif_resiliation' => 'required|in:depart_volontaire,non_paiement,fin_bail,autre',
        ]);

        $contrat->update([
            'statut'            => 'resilie',
            'motif_resiliation' => $request['motif_resiliation'],
            'date_resiliation'  => now(),
        ]);

        // Le bien redevient disponible
        $contrat->bien->update(['statut' => 'disponible']);

        return redirect()->route('contrats.index')
                         ->with('success', 'Contrat résilié. Le bien est de nouveau disponible.');
    }
}