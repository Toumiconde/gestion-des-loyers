<?php

namespace App\Http\Controllers;

use App\Models\UniteLocative;
use App\Models\Bien;
use Illuminate\Http\Request;

class UniteLocativeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bien_id' => 'required|exists:biens,id',
            'libelle' => 'required|string|max:255',
            'niveau' => 'required|integer',
            'prix_loyer' => 'required|numeric',
            'nombre_chambres' => 'required|integer',
            'surface' => 'nullable|numeric',
        ]);

        UniteLocative::create($request->all());

        return back()->with('success', 'Unité locative ajoutée avec succès.');
    }

    public function update(Request $request, UniteLocative $unites_locative)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'niveau' => 'required|integer',
            'prix_loyer' => 'required|numeric',
            'nombre_chambres' => 'required|integer',
        ]);

        $unites_locative->update($request->all());

        return back()->with('success', 'Unité locative mise à jour.');
    }

    public function destroy(UniteLocative $unites_locative)
    {
        $unites_locative->delete();
        return back()->with('success', 'Unité locative supprimée.');
    }
}
