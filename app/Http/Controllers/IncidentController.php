<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Contrat;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Incident::with('contrat.locataire', 'contrat.bien')->orderBy('priorite', 'desc');
        $maintenanceRequests = collect();

        if ($user->isProprietaire()) {
            if (!$user->proprietaire) abort(403, 'Profil non configuré.');
            $proprietaireId = $user->proprietaire->id;
            $query->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId));
            $maintenanceRequests = MaintenanceRequest::where('user_id', $user->id)->latest()->get();
            
        } elseif ($user->isLocataire()) {
            $locataireId = $user->locataire->id ?? 0;
            $query->whereHas('contrat', fn($q) => $q->where('locataire_id', $locataireId));
            
        } elseif ($user->isAdmin() || $user->isGestionnaire() || $user->isComptable()) {
            // Le staff voit TOUT
            $maintenanceRequests = MaintenanceRequest::with('user')->latest()->get();
        }

        $incidents = $query->paginate(10);
        return view('incidents.index', compact('incidents', 'maintenanceRequests'));
    }

    public function create()
    {
        if (auth()->user()->isProprietaire()) {
            abort(403, 'Un propriétaire ne peut pas déclarer d\'incident.');
        }

        $query = Contrat::where('statut', 'actif')->with('locataire', 'bien');
        if (auth()->user()->isLocataire() && auth()->user()->locataire) {
            $query->where('locataire_id', auth()->user()->locataire->id);
        }

        $contrats = $query->get();
        return view('incidents.create', compact('contrats'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isProprietaire()) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'contrat_id'  => 'nullable|exists:contrats,id',
            'objet'       => 'required|string|max:200',
            'type'        => 'required|string',
            'description' => 'required|string',
            'priorite'    => 'required|in:basse,moyenne,haute',
            'photo'       => 'nullable|image|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $contratId = $validated['contrat_id'];

        // Si c'est un locataire, on force son contrat actif s'il n'est pas fourni
        if ($user->isLocataire()) {
            $locataire = $user->locataire;
            $activeContrat = $locataire->contratActif;
            
            if (!$activeContrat) {
                return back()->with('error', 'Aucun contrat actif trouvé pour déclarer un incident.');
            }
            
            $contratId = $activeContrat->id;
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('incidents', 'public');
        }

        $incident = Incident::create([
            'contrat_id'     => $contratId,
            'declare_par'    => $user->id,
            'titre'          => '[' . strtoupper($validated['type']) . '] ' . $validated['objet'],
            'description'    => $validated['description'],
            'priorite'       => $validated['priorite'] === 'basse' ? 'faible' : ($validated['priorite'] === 'haute' ? 'urgent' : 'moyen'),
            'photo_incident' => $photoPath,
            'statut'         => 'ouvert',
        ]);

        // Log d'activité
        \App\Models\ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'creation',
            'target_type' => 'incident',
            'target_id'   => $incident->id,
            'description' => "a déclaré un nouvel incident : " . $incident->titre,
        ]);

        return redirect()->route('dashboard')
                         ->with('success', 'Votre signalement a été envoyé avec succès. Un gestionnaire va l\'étudier rapidement.');
    }

    public function show(Incident $incident)
    {
        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire) {
            if ($incident->contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
                abort(403, 'Cet incident ne vous concerne pas.');
            }
        } elseif (auth()->user()->isLocataire() && auth()->user()->locataire) {
            if ($incident->contrat->locataire_id !== auth()->user()->locataire->id) {
                abort(403, 'Cet incident ne vous concerne pas.');
            }
        }

        $incident->load('contrat.locataire', 'contrat.bien', 'declarePar');
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        if (auth()->user()->isProprietaire() || auth()->user()->isLocataire()) {
            abort(403, 'Vous ne pouvez pas modifier un incident.');
        }
        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire() && auth()->user()->role !== 'comptable') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'statut'          => 'required|in:ouvert,en_devis,en_travaux,resolu,paye',
            'cout_estime'     => 'nullable|numeric|min:0',
            'cout_reel'       => 'nullable|numeric|min:0',
            'technicien_nom'  => 'nullable|string|max:255',
            'technicien_tel'  => 'nullable|string|max:20',
            'date_resolution' => 'nullable|date',
        ]);

        // Si le statut passe à "paye" et n'était pas déjà "paye"
        if ($validated['statut'] === 'paye' && $incident->statut !== 'paye') {
            if (!$validated['cout_reel'] && !$incident->cout_reel) {
                return back()->with('error', 'Veuillez saisir un coût réel pour marquer comme payé.');
            }

            // Création automatique d'une dépense
            \App\Models\Depense::create([
                'libelle'      => "Réparation : " . $incident->titre . " (" . $incident->contrat->bien->libelle . ")",
                'categorie'    => 'maintenance',
                'montant'      => $validated['cout_reel'] ?? $incident->cout_reel,
                'date_depense' => now(),
                'notes'        => "Généré automatiquement depuis l'incident #" . $incident->id,
                'created_by'   => auth()->id(),
            ]);
        }

        $incident->update($validated);

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Incident mis à jour et comptabilisé.');
    }

    public function destroy(Incident $incident)
    {
        if (auth()->user()->isProprietaire() || auth()->user()->isLocataire()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $incident->delete();
        return redirect()->route('incidents.index')
                         ->with('success', 'Incident supprimé.');
    }
}