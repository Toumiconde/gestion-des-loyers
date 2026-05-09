<?php

namespace App\Http\Controllers;

use App\Models\Maintenancier;
use Illuminate\Http\Request;

class MaintenancierController extends Controller
{
    public function index()
    {
        $maintenanciers = Maintenancier::withCount('incidents')->latest()->paginate(15);
        return view('maintenanciers.index', compact('maintenanciers'));
    }

    public function create()
    {
        return view('maintenanciers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'          => 'required|string|max:255',
            'specialite'   => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'disponibilite'=> 'required|in:disponible,occupe,indisponible',
            'notes'        => 'nullable|string',
        ]);

        Maintenancier::create($validated);

        return redirect()->route('maintenanciers.index')
                         ->with('success', 'Maintenancier ajouté avec succès.');
    }

    public function edit(Maintenancier $maintenancier)
    {
        return view('maintenanciers.edit', compact('maintenancier'));
    }

    public function update(Request $request, Maintenancier $maintenancier)
    {
        $validated = $request->validate([
            'nom'          => 'required|string|max:255',
            'specialite'   => 'required|string|max:255',
            'telephone'    => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'disponibilite'=> 'required|in:disponible,occupe,indisponible',
            'notes'        => 'nullable|string',
        ]);

        $maintenancier->update($validated);

        return redirect()->route('maintenanciers.index')
                         ->with('success', 'Maintenancier mis à jour.');
    }

    public function destroy(Maintenancier $maintenancier)
    {
        $maintenancier->delete();
        return redirect()->route('maintenanciers.index')
                         ->with('success', 'Maintenancier supprimé.');
    }
}
