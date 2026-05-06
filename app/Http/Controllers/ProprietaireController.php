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
        $proprietaires = Proprietaire::with('user')->paginate(10);
        return view('proprietaires.index', compact('proprietaires'));
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
            'rib_bancaire' => 'nullable|string|max:100',
        ]);

        // On crée d'abord le compte user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role'     => 'proprietaire',
        ]);

        // Puis le profil propriétaire lié à ce user
        Proprietaire::create([
            'user_id'      => $user->id,
            'telephone'    => $validated['telephone'],
            'adresse'      => $validated['adresse'],
            'rib_bancaire' => $validated['rib_bancaire'],
        ]);

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
            'rib_bancaire' => 'nullable|string|max:100',
        ]);

        // Mise à jour du user lié
        $proprietaire->user->update(['name' => $validated['name']]);

        // Mise à jour du profil propriétaire
        $proprietaire->update([
            'telephone'    => $validated['telephone'],
            'adresse'      => $validated['adresse'],
            'rib_bancaire' => $validated['rib_bancaire'],
        ]);

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
