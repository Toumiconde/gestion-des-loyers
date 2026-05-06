<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::all();
        return view('parametres.index', compact('parametres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cle'         => 'required|string|max:100|unique:parametres,cle',
            'valeur'      => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['updated_by'] = auth()->id();
        Parametre::create($validated);

        return redirect()->route('parametres.index')->with('success', 'Paramètre ajouté.');
    }

    public function update(Request $request, Parametre $parametre)
    {
        $validated = $request->validate([
            'valeur'      => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['updated_by'] = auth()->id();
        $parametre->update($validated);

        return redirect()->route('parametres.index')->with('success', 'Paramètre mis à jour.');
    }

    public function create() { return view('parametres.create'); }
    public function show(Parametre $parametre) { return view('parametres.show', compact('parametre')); }
    public function edit(Parametre $parametre) { return view('parametres.edit', compact('parametre')); }
    public function destroy(Parametre $parametre) { $parametre->delete(); return redirect()->route('parametres.index'); }
}