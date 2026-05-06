<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\Request;

class LocataireController extends Controller
{
    public function index()
    {
        $locataires = Locataire::with('contratActif.bien')->paginate(10);
        return view('locataires.index', compact('locataires'));
    }

    public function create()
    {
        return view('locataires.create');
    }

    public function store(Request $request)
    {
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
        $locataire->load('contrats.bien', 'contrats.paiements');
        return view('locataires.show', compact('locataire'));
    }

    public function edit(Locataire $locataire)
    {
        return view('locataires.edit', compact('locataire'));
    }

    public function update(Request $request, Locataire $locataire)
    {
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
        if ($locataire->contratActif) {
            return redirect()->route('locataires.index')
                             ->with('error', 'Impossible de supprimer un locataire avec un contrat actif.');
        }

        $locataire->delete();

        return redirect()->route('locataires.index')
                         ->with('success', 'Locataire supprimé.');
    }
}