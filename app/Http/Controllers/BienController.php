<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Proprietaire;
use Illuminate\Http\Request;

class BienController extends Controller
{
    private function authorizeEditor()
    {
        // Les admins, gestionnaires et propriétaires peuvent éditer/créer des biens
        if (!auth()->user()->isAdmin() && !auth()->user()->isProprietaire()) {
            abort(403, 'Accès non autorisé - Seuls les administrateurs et propriétaires peuvent gérer les biens.');
        }
    }

    public function index(Request $request)
    {
        $selectedYear = $request->get('year', session('selected_year', date('Y')));
        $selectedMonth = $request->get('month');
        
        $endOfYear = \Carbon\Carbon::create($selectedYear, 12, 31)->endOfDay();

        $query = Bien::with('proprietaire.user')
                    ->withTrashed()
                    ->whereYear('created_at', $selectedYear);

        if (auth()->user()->isAdmin() && $selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if (auth()->user()->isProprietaire()) {
            if (!auth()->user()->proprietaire) {
                abort(403, 'Profil propriétaire non configuré.');
            }
            $proprietaireId = auth()->user()->proprietaire->id;
            $query->where('proprietaire_id', $proprietaireId);
        } elseif (auth()->user()->isLocataire()) {
            $locataireId = auth()->user()->locataire->id ?? 0;
            $query->whereHas('contrats', function($q) use ($locataireId) {
                $q->where('locataire_id', $locataireId)->where('statut', 'actif');
            });
        }

        $biens = $query->paginate(15);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('biens.index', compact('biens', 'selectedYear', 'selectedMonth', 'months'));
    }

    public function create()
    {
        $this->authorizeEditor();
        $proprietaires = Proprietaire::with('user')->get();
        return view('biens.create', compact('proprietaires'));
    }

    public function store(Request $request)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'libelle'         => 'required|string|max:200',
            'type'            => 'required|in:appartement,maison,studio,bureau,commerce,autre',
            'adresse'         => 'required|string',
            'surface'         => 'nullable|numeric|min:1',
            'loyer_base'      => 'required|numeric|min:1',
            'charges'         => 'nullable|numeric|min:0',
            'depot_garantie'  => 'nullable|numeric|min:0',
            'nombre_chambres' => 'required|integer|min:0',
            'type_douche'     => 'required|in:interne,externe',
        ]);

        $bien = Bien::create($validated);
        
        // Gérer l'upload de la photo si présente
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('biens', 'public');
            $bien->documents()->create([
                'nom' => 'Photo Principale',
                'type' => 'photo',
                'chemin' => $path,
                'taille_ko' => round($request->file('photo')->getSize() / 1024),
                'uploaded_by' => auth()->id(),
            ]);
        }

        // Créer automatiquement une unité locative par défaut pour que le bien soit visible en recherche
        \App\Models\UniteLocative::create([
            'bien_id' => $bien->id,
            'libelle' => 'Logement Complet',
            'niveau' => 0,
            'nombre_chambres' => $bien->nombre_chambres,
            'prix_loyer' => $bien->loyer_base,
            'statut' => 'libre',
            'description' => "Ceci est l'unité principale pour le bien {$bien->libelle}.",
        ]);

        // Notification à l'administrateur
        $admins = \App\Models\User::where('role', 'admin')->get();
        $proprietaireName = $bien->proprietaire->user->name;
        
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ProfileUpdated(auth()->user(), "🏠 Nouveau Bien Ajouté : Le propriétaire {$proprietaireName} vient d'ajouter le bien '{$bien->libelle}' ({$bien->nombre_chambres} ch, douche {$bien->type_douche})."));

        return redirect()->route('biens.index')
                         ->with('success', 'Bien ajouté avec succès et l\'administrateur a été notifié.');
    }

    public function show(Bien $bien)
    {
        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire && $bien->proprietaire_id !== auth()->user()->proprietaire->id) {
            abort(403, 'Ce bien ne vous appartient pas.');
        }

        if (auth()->user()->isLocataire() && auth()->user()->locataire) {
            $isOccupant = $bien->contrats()->where('locataire_id', auth()->user()->locataire->id)->where('statut', 'actif')->exists();
            if (!$isOccupant) {
                abort(403, 'Vous ne pouvez pas consulter les détails de ce bien.');
            }
        }

        $bien->load('proprietaire.user', 'contrats.locataire', 'documents');
        return view('biens.show', compact('bien'));
    }

    public function edit(Bien $bien)
    {
        $this->authorizeEditor();
        $proprietaires = Proprietaire::with('user')->get();
        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    public function update(Request $request, Bien $bien)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'libelle'        => 'required|string|max:200',
            'type'           => 'required|in:appartement,maison,studio,bureau,commerce,autre',
            'adresse'        => 'required|string',
            'surface'        => 'nullable|numeric|min:1',
            'loyer_base'     => 'required|numeric|min:1',
            'charges'        => 'nullable|numeric|min:0',
            'depot_garantie' => 'nullable|numeric|min:0',
            'statut'         => 'required|in:disponible,occupe,en_travaux,archive',
        ]);

        $bien->update($validated);

        return redirect()->route('biens.index')
                         ->with('success', 'Bien mis à jour.');
    }

    public function destroy(Bien $bien)
    {
        $this->authorizeEditor();
        if ($bien->contratActif) {
            return redirect()->route('biens.index')
                             ->with('error', 'Impossible de supprimer un bien avec un contrat actif.');
        }

        $bien->delete();

        return redirect()->route('biens.index')
                         ->with('success', 'Bien supprimé.');
    }
}