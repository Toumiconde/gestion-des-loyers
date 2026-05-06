<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Proprietaire;
use Illuminate\Http\Request;

class BienController extends Controller
{
    public function index()
    {
        $biens = Bien::with('proprietaire.user')->paginate(10);
        return view('biens.index', compact('biens'));
    }

    public function create()
    {
        // On passe la liste des propriétaires pour le formulaire
        $proprietaires = Proprietaire::with('user')->get();
        return view('biens.create', compact('proprietaires'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'libelle'         => 'required|string|max:200',
            'type'            => 'required|in:appartement,maison,studio,bureau,commerce,autre',
            'adresse'         => 'required|string',
            'surface'         => 'nullable|numeric|min:1',
            'loyer_base'      => 'required|numeric|min:1',
            'charges'         => 'nullable|numeric|min:0',
            'depot_garantie'  => 'nullable|numeric|min:0',
        ]);

        Bien::create($validated);

        return redirect()->route('biens.index')
                         ->with('success', 'Bien ajouté avec succès.');
    }

    public function show(Bien $bien)
    {
        $bien->load('proprietaire.user', 'contrats.locataire', 'documents');
        return view('biens.show', compact('bien'));
    }

    public function edit(Bien $bien)
    {
        $proprietaires = Proprietaire::with('user')->get();
        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    public function update(Request $request, Bien $bien)
    {
        $validated = $request->validate([
            'libelle'        => 'required|string|max:200',
            'type'           => 'required|in:appartement,maison,studio,bureau,commerce,autre',
            'adresse'        => 'required|string',
            'surface'        => 'nullable|numeric|min:1',
            'loyer_base'     => 'required|numeric|min:1',
            'charges'        => 'nullable|numeric|min:0',
            'depot_garantie' => 'nullable|numeric|min:0',
            'statut'         => 'required|in:disponible,occupe,en_travaux,archive',
        ]);

        $bien->update($validated);

        return redirect()->route('biens.index')
                         ->with('success', 'Bien mis à jour.');
    }

    public function destroy(Bien $bien)
    {
        // On vérifie qu'il n'y a pas de contrat actif avant de supprimer
        if ($bien->contratActif) {
            return redirect()->route('biens.index')
                             ->with('error', 'Impossible de supprimer un bien avec un contrat actif.');
        }

        $bien->delete();

        return redirect()->route('biens.index')
                         ->with('success', 'Bien supprimé.');
    }
}