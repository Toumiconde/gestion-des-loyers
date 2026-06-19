<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Http\Request;

class ProprietaireController extends Controller
{
    // Liste tous les propriétaires
    public function index()
    {
        $selectedYear = session('selected_year', date('Y'));
        $endOfYear = \Carbon\Carbon::create($selectedYear, 12, 31)->endOfDay();

        $proprietaires = Proprietaire::with('user')
                        ->withTrashed()
                        ->whereYear('created_at', $selectedYear)
                        ->paginate(10);

        return view('proprietaires.index', compact('proprietaires', 'selectedYear'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('proprietaires.create');
    }

    // Enregistre un nouveau propriétaire
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8',
            'telephone'    => 'nullable|string|max:20',
            'adresse'      => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'rib_bancaire' => 'nullable|string|max:100',
            'nom_banque'   => 'nullable|string|max:100',
            'titulaire_compte' => 'nullable|string|max:100',
        ]);

        // On crée d'abord le compte user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role'     => 'proprietaire',
        ]);

        // Puis le profil propriétaire lié à ce user
        $proprietaireData = [
            'user_id'      => $user->id,
            'commission_rate' => $validated['commission_rate'],
            'telephone'    => $validated['telephone'],
            'adresse'      => $validated['adresse'],
            'rib_bancaire' => $validated['rib_bancaire'],
            'nom_banque'   => $validated['nom_banque'] ?? null,
            'titulaire_compte' => $validated['titulaire_compte'] ?? null,
        ];

        // Gestion de la signature (Fichier ou Dessin)
        if ($request->filled('signature_data')) {
            $data = $request->input('signature_data');
            $image = str_replace('data:image/png;base64,', '', $data);
            $image = str_replace(' ', '+', $image);
            $imageName = 'signatures/sig_' . time() . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($image));
            $proprietaireData['signature'] = $imageName;
        } elseif ($request->hasFile('signature')) {
            $proprietaireData['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $proprietaire = Proprietaire::create($proprietaireData);

        // Log d'activité
        \App\Models\ActivityLog::log('creation', "Création du propriétaire {$user->name}", $proprietaire);

        return redirect()->route('proprietaires.index')
                         ->with('success', 'Propriétaire créé avec succès.');
    }

    // Affiche un propriétaire et ses biens
    public function show(Proprietaire $proprietaire)
    {
        $proprietaire->load('user', 'biens');
        return view('proprietaires.show', compact('proprietaire'));
    }

    // Formulaire de modification
    public function edit(Proprietaire $proprietaire)
    {
        $proprietaire->load('user');
        return view('proprietaires.edit', compact('proprietaire'));
    }

    // Enregistre les modifications
    public function update(Request $request, Proprietaire $proprietaire)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'telephone'    => 'nullable|string|max:20',
            'adresse'      => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'rib_bancaire' => 'nullable|string|max:100',
            'nom_banque'   => 'nullable|string|max:100',
            'titulaire_compte' => 'nullable|string|max:100',
            'signature'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Mise à jour du user lié
        $proprietaire->user->update(['name' => $validated['name']]);

        // Gestion de la signature (Fichier ou Dessin)
        if ($request->filled('signature_data')) {
            // C'est une signature dessinée (Base64)
            if ($proprietaire->signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($proprietaire->signature);
            }
            $data = $request->input('signature_data');
            $image = str_replace('data:image/png;base64,', '', $data);
            $image = str_replace(' ', '+', $image);
            $imageName = 'signatures/sig_' . time() . '_' . $proprietaire->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($image));
            $validated['signature'] = $imageName;
        } elseif ($request->hasFile('signature')) {
            // C'est un téléversement classique
            if ($proprietaire->signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($proprietaire->signature);
            }
            $validated['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        // Mise à jour du profil propriétaire
        $proprietaire->update($validated);

        // Log d'activité
        \App\Models\ActivityLog::log('modification', "Mise à jour du profil de {$proprietaire->user->name}", $proprietaire);

        return redirect()->route('proprietaires.index')
                         ->with('success', 'Propriétaire mis à jour.');
    }

    // Supprime un propriétaire
    public function destroy(Proprietaire $proprietaire)
    {
        // On supprime le user, le propriétaire sera supprimé automatiquement
        // grâce au cascadeOnDelete() dans la migration
        $proprietaire->user->delete();

        return redirect()->route('proprietaires.index')
                         ->with('success', 'Propriétaire supprimé.');
    }
}
