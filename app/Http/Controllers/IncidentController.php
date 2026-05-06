<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Contrat;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with('contrat.locataire', 'contrat.bien')
                             ->orderBy('priorite', 'desc')
                             ->paginate(10);
        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        $contrats = Contrat::where('statut', 'actif')->with('locataire', 'bien')->get();
        return view('incidents.create', compact('contrats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrat_id'  => 'required|exists:contrats,id',
            'titre'       => 'required|string|max:200',
            'description' => 'required|string',
            'priorite'    => 'required|in:faible,moyen,urgent',
        ]);

        $validated['declare_par'] = auth()->id();

        Incident::create($validated);

        return redirect()->route('incidents.index')
                         ->with('success', 'Incident déclaré avec succès.');
    }

    public function show(Incident $incident)
    {
        $incident->load('contrat.locataire', 'contrat.bien', 'declarePar');
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'statut'          => 'required|in:ouvert,en_cours,resolu,ferme',
            'date_resolution' => 'nullable|date',
        ]);

        $incident->update($validated);

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Incident mis à jour.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('incidents.index')
                         ->with('success', 'Incident supprimé.');
    }
}