<?php

namespace App\Http\Controllers;

use App\Models\Quittance;
use Illuminate\Http\Request;

class QuittanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quittances = Quittance::with('paiement.contrat.locataire', 'paiement.contrat.bien')->paginate(10);
        return view('quittances.index', compact('quittances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quittances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(403, 'La création manuelle de quittance est désactivée.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quittance $quittance)
    {
        $quittance->load('paiement.contrat.locataire', 'paiement.contrat.bien.proprietaire.user');
        return view('quittances.show', compact('quittance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quittance $quittance)
    {
        return view('quittances.edit', compact('quittance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quittance $quittance)
    {
        abort(403, 'Une quittance ne peut pas être modifiée.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quittance $quittance)
    {
        abort(403, 'Une quittance ne peut pas être supprimée.');
    }
}
