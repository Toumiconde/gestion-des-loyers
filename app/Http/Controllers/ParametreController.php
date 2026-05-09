<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParametreController extends Controller
{
    public function index(Request $request)
    {
        $settings = Parametre::all()->pluck('valeur', 'cle');
        
        $search = $request->get('search');
        $letter = $request->get('letter');

        $results = [
            'locataires' => [],
            'biens'      => [],
            'documents'  => [],
        ];

        if ($search || $letter) {
            // Recherche Locataires
            $results['locataires'] = \App\Models\Locataire::query()
                ->when($search, function($q) use ($search) {
                    $q->where('nom', 'LIKE', "%{$search}%")->orWhere('prenom', 'LIKE', "%{$search}%");
                })
                ->when($letter, function($q) use ($letter) {
                    $q->where('nom', 'LIKE', "{$letter}%");
                })
                ->take(10)->get();

            // Recherche Propriétaires
            $results['proprietaires'] = \App\Models\Proprietaire::query()
                ->whereHas('user', function($q) use ($search, $letter) {
                    $q->when($search, function($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->when($letter, function($sq) use ($letter) {
                        $sq->where('name', 'LIKE', "{$letter}%");
                    });
                })
                ->with('user')
                ->take(10)->get();

            // Recherche Biens
            $results['biens'] = \App\Models\Bien::query()
                ->when($search, function($q) use ($search) {
                    $q->where('libelle', 'LIKE', "%{$search}%")->orWhere('adresse', 'LIKE', "%{$search}%");
                })
                ->when($letter, function($q) use ($letter) {
                    $q->where('libelle', 'LIKE', "{$letter}%");
                })
                ->take(10)->get();

            // Recherche Documents
            $results['documents'] = \App\Models\Document::query()
                ->when($search, function($q) use ($search) {
                    $q->where('nom', 'LIKE', "%{$search}%");
                })
                ->when($letter, function($q) use ($letter) {
                    $q->where('nom', 'LIKE', "{$letter}%");
                })
                ->take(10)->get();
        }

        return view('parametres.index', compact('settings', 'results', 'search', 'letter'));
    }

    public function edit()
    {
        $settings = Parametre::all()->pluck('valeur', 'cle');
        return view('parametres.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nom_agence'    => 'required|string|max:255',
            'email_contact' => 'required|email|max:255',
            'telephone'     => 'required|string|max:50',
            'adresse'       => 'required|string|max:500',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'devise'        => 'required|string|max:10',
            // Notifications
            'seuil_retard'  => 'nullable|integer|min:0',
            'alerte_sms'    => 'nullable|string',
            'alerte_email'  => 'nullable|string',
            // Fiscalité
            'tva_taux'      => 'nullable|numeric|min:0',
            'format_quittance' => 'nullable|string|max:50',
            // Sécurité
            'expiration_session' => 'nullable|integer|min:1',
            // Guides PDF
            'guide_admin'       => 'nullable|file|mimes:pdf|max:10240',
            'guide_gestionnaire' => 'nullable|file|mimes:pdf|max:10240',
            'guide_locataire'    => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // On s'assure que les alertes sont bien dans le tableau de sauvegarde
        $paramsToSave = $validated;
        if (!$request->has('alerte_email')) $paramsToSave['alerte_email'] = 'off';
        if (!$request->has('alerte_sms')) $paramsToSave['alerte_sms'] = 'off';

        foreach ($paramsToSave as $key => $value) {
            if (in_array($key, ['logo', 'signature', 'guide_admin', 'guide_gestionnaire', 'guide_locataire']) && $request->hasFile($key)) {
                $oldFile = Parametre::where('cle', $key)->value('valeur');
                if ($oldFile) Storage::disk('public')->delete($oldFile);
                $path = $request->file($key)->store('agency/docs', 'public');
                Parametre::updateOrCreate(['cle' => $key], ['valeur' => $path, 'updated_by' => auth()->id()]);
            } else if (!in_array($key, ['logo', 'signature', 'guide_admin', 'guide_gestionnaire', 'guide_locataire'])) {
                Parametre::updateOrCreate(['cle' => $key], ['valeur' => $value, 'updated_by' => auth()->id()]);
            }
        }

        return redirect()->route('parametres.index')->with('success', 'Configuration mise à jour avec succès.');
    }
}