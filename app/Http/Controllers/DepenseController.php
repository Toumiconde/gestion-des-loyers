<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');

        $query = Depense::whereYear('date_depense', $selectedYear);
        if ($selectedMonth) {
            $query->whereMonth('date_depense', $selectedMonth);
        }

        $depenses = (clone $query)->orderBy('date_depense', 'desc')->paginate(15);
        
        $total = (clone $query)->sum('montant');
        $parCategorie = (clone $query)->selectRaw('categorie, sum(montant) as total')
            ->groupBy('categorie')
            ->get();

        return view('depenses.index', compact('depenses', 'total', 'parCategorie'));
    }

    public function create()
    {
        $categories = ['maintenance', 'salaires', 'loyer_agence', 'impots', 'divers'];
        return view('depenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'      => 'required|string|max:255',
            'categorie'    => 'required|in:maintenance,salaires,loyer_agence,impots,divers',
            'montant'      => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        Depense::create($validated);

        return redirect()->route('depenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    public function destroy(Depense $depense)
    {
        $depense->delete();
        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée.');
    }
}
