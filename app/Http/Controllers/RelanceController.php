<?php

namespace App\Http\Controllers;

use App\Models\Relance;
use App\Models\Contrat;
use Illuminate\Http\Request;

class RelanceController extends Controller
{
    public function index()
    {
        $selectedYear = session('selected_year', date('Y'));
        $relances = Relance::with('contrat.locataire')
            ->whereYear('date_envoi', $selectedYear)
            ->paginate(10);
        return view('relances.index', compact('relances', 'selectedYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrat_id' => 'required|exists:contrats,id',
            'niveau'     => 'required|in:niveau_1,niveau_2,niveau_3',
            'canal'      => 'required|in:email,sms,email_sms',
        ]);

        $validated['date_envoi'] = now();

        Relance::create($validated);

        return redirect()->back()->with('success', 'Relance envoyée.');
    }

    public function update(Request $request, Relance $relance)
    {
        // Acquitter une relance manuellement
        $relance->update([
            'statut'             => 'acquittee',
            'acquitte_par'       => auth()->id(),
            'date_acquittement'  => now(),
        ]);

        return redirect()->back()->with('success', 'Relance acquittée.');
    }

    public function create() { return view('relances.create'); }
    public function show(Relance $relance) { return view('relances.show', compact('relance')); }
    public function edit(Relance $relance) { return view('relances.edit', compact('relance')); }
    public function destroy(Relance $relance) { $relance->delete(); return redirect()->route('relances.index'); }
}