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
        $contratId = $validated['contrat_id'] ?? null;

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

        // Notifier tous les Admins et Gestionnaires
        $staff = \App\Models\User::whereIn('role', ['admin', 'gestionnaire'])->get();
        foreach ($staff as $staffMember) {
            $staffMember->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\NouvelIncident',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $staffMember->id,
                'data'            => json_encode([
                    'message' => '🚨 Nouvel incident signalé : <strong>' . $incident->titre . '</strong>',
                    'url'     => route('incidents.index'),
                ]),
            ]);
        }

        return redirect()->route('dashboard')
                         ->with('success', 'Votre signalement a été envoyé avec succès. Un gestionnaire va l\'étudier rapidement.');
    }

    public function show(Incident $incident)
    {
        $user = auth()->user();

        if ($user->isProprietaire() && $user->proprietaire) {
            if ($incident->contrat->bien->proprietaire_id !== $user->proprietaire->id) {
                abort(403, 'Cet incident ne vous concerne pas.');
            }
        } elseif ($user->isLocataire() && $user->locataire) {
            if ($incident->contrat->locataire_id !== $user->locataire->id) {
                abort(403, 'Cet incident ne vous concerne pas.');
            }
        }

        // Marquer comme vu dès qu'un membre du staff ouvre la fiche
        if ($incident->is_new && ($user->isAdmin() || $user->isGestionnaire() || $user->isComptable())) {
            $incident->is_new = false;
            $incident->save();
        }

        $incident->load('contrat.locataire', 'contrat.bien', 'declarePar', 'maintenancier');
        
        $maintenanciers = collect();
        if ($user->isAdmin() || $user->isGestionnaire()) {
            $maintenanciers = \App\Models\Maintenancier::where('disponibilite', 'disponible')->get();
        }

        return view('incidents.show', compact('incident', 'maintenanciers'));
    }

    public function edit(Incident $incident)
    {
        if (auth()->user()->isProprietaire() || auth()->user()->isLocataire()) {
            abort(403, 'Vous ne pouvez pas modifier un incident.');
        }
        
        $maintenanciers = \App\Models\Maintenancier::where('disponibilite', 'disponible')->get();
        return view('incidents.edit', compact('incident', 'maintenanciers'));
    }

    public function update(Request $request, Incident $incident)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire() && auth()->user()->role !== 'comptable') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'statut'           => 'required|in:ouvert,en_devis,en_travaux,resolu,paye',
            'devis_montant'    => 'nullable|numeric|min:0',
            'devis_note'       => 'nullable|string',
            'cout_reel'        => 'nullable|numeric|min:0',
            'maintenancier_id' => 'nullable|exists:maintenanciers,id',
            'technicien_nom'   => 'nullable|string|max:255',
            'technicien_tel'   => 'nullable|string|max:20',
            'date_resolution'  => 'nullable|date',
        ]);

        // On remplit le modèle avec les données validées
        $incident->fill($validated);

        // Si le statut passe à "paye" et n'était pas déjà "paye"
        if ($incident->isDirty('statut') && $incident->statut === 'paye') {
            if (empty($incident->cout_reel) && $incident->cout_reel != '0') {
                return back()->with('error', 'Veuillez saisir un coût réel pour marquer comme payé.');
            }

            // Création automatique d'une dépense
            \App\Models\Depense::create([
                'libelle'      => "Réparation : " . $incident->titre . " (" . $incident->contrat->bien->libelle . ")",
                'categorie'    => 'maintenance',
                'montant'      => $incident->cout_reel,
                'date_depense' => now(),
                'notes'        => "Généré automatiquement depuis l'incident #" . $incident->id,
                'created_by'   => auth()->id(),
            ]);
        }

        // Si le statut passe à "en_devis", on synchronise les champs et on envoie auto au proprio
        if ($incident->statut === 'en_devis' && $incident->devis_statut !== 'accepte') {
            if (empty($incident->devis_montant) && $incident->devis_montant != '0') {
                return back()->with('error', 'Veuillez saisir le Montant du Devis pour passer à ce statut.');
            }
            
            // On envoie seulement si ça n'a pas déjà été envoyé
            if ($incident->devis_statut !== 'envoye_proprio') {
                $incident->devis_statut  = 'envoye_proprio';
                $incident->devis_envoye_at = now();
                
                // Notifier le propriétaire
                $proprietaireUser = $incident->contrat->bien->proprietaire->user ?? null;
                if ($proprietaireUser) {
                    $proprietaireUser->notifications()->create([
                        'id'              => \Illuminate\Support\Str::uuid(),
                        'type'            => 'App\Notifications\DevisIncident',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id'   => $proprietaireUser->id,
                        'data'            => json_encode([
                            'message' => '📋 Un devis de <strong>' . number_format($incident->devis_montant, 0, ',', ' ') . ' GNF</strong> attend votre validation pour l\'incident : <strong>' . $incident->titre . '</strong>',
                            'url'     => route('incidents.show', $incident),
                        ]),
                    ]);
                }
            }
        }

        $incident->save();

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Incident mis à jour avec succès.');
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

    // ================================================================
    // WORKFLOW : ÉTAPE 2 - Gestionnaire assigne un maintenancier + devis
    // ================================================================
    public function assignerMaintenancier(Request $request, Incident $incident)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire()) {
            abort(403);
        }

        $request->validate([
            'maintenancier_id' => 'required|exists:maintenanciers,id',
            'devis_montant'    => 'required|numeric|min:0',
            'devis_note'       => 'nullable|string|max:1000',
        ]);

        $incident->maintenancier_id = $request->maintenancier_id;
        $incident->devis_montant    = $request->devis_montant;
        $incident->devis_note       = $request->devis_note;
        $incident->devis_statut     = 'en_attente';
        $incident->statut           = 'en_devis';
        $incident->save();

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Maintenancier assigné et devis saisi. Vous pouvez maintenant envoyer le devis au propriétaire.');
    }

    // ================================================================
    // WORKFLOW : ÉTAPE 3 - Gestionnaire envoie le devis au propriétaire
    // ================================================================
    public function envoyerDevisProprietaire(Incident $incident)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire()) {
            abort(403);
        }

        if (empty($incident->devis_montant) && $incident->devis_montant != '0') {
            return back()->with('error', 'Veuillez d\'abord saisir un devis avant de l\'envoyer.');
        }

        // On passe le statut général de l'incident à "en_devis" s'il était encore sur "ouvert"
        if ($incident->statut === 'ouvert') {
            $incident->statut = 'en_devis';
        }

        $incident->devis_statut     = 'envoye_proprio';
        $incident->devis_envoye_at  = now();
        $incident->save();

        // Notifier le propriétaire concerné
        $proprietaireUser = $incident->contrat->bien->proprietaire->user ?? null;
        if ($proprietaireUser) {
            $proprietaireUser->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\DevisIncident',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $proprietaireUser->id,
                'data'            => json_encode([
                    'message' => '📋 Un devis de <strong>' . number_format($incident->devis_montant, 0, ',', ' ') . ' GNF</strong> attend votre validation pour l\'incident : <strong>' . $incident->titre . '</strong>',
                    'url'     => route('incidents.show', $incident),
                ]),
            ]);
        }

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Devis envoyé au propriétaire. En attente de sa validation.');
    }

    // ================================================================
    // WORKFLOW : ÉTAPE 4a - Propriétaire ACCEPTE le devis
    // ================================================================
    public function accepterDevis(Incident $incident)
    {
        if (!auth()->user()->isProprietaire()) {
            abort(403);
        }

        if ($incident->contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
            abort(403, 'Cet incident ne vous appartient pas.');
        }

        $incident->devis_statut    = 'accepte';
        $incident->devis_valide_at = now();
        $incident->statut          = 'en_travaux';
        $incident->save();

        // Notifier tous les gestionnaires
        $gestionnaires = \App\Models\User::where('role', 'gestionnaire')->get();
        foreach ($gestionnaires as $g) {
            $g->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\DevisAccepte',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $g->id,
                'data'            => json_encode([
                    'message' => '✅ Le propriétaire a <strong>accepté</strong> le devis de ' . number_format($incident->devis_montant, 0, ',', ' ') . ' GNF pour : <strong>' . $incident->titre . '</strong>. Les travaux peuvent commencer.',
                    'url'     => route('incidents.show', $incident),
                ]),
            ]);
        }

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Devis accepté. Les travaux sont maintenant autorisés.');
    }

    // ================================================================
    // WORKFLOW : ÉTAPE 4b - Propriétaire REFUSE le devis
    // ================================================================
    public function refuserDevis(Request $request, Incident $incident)
    {
        if (!auth()->user()->isProprietaire()) {
            abort(403);
        }

        if ($incident->contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
            abort(403, 'Cet incident ne vous appartient pas.');
        }

        $request->validate([
            'refus_note' => 'required|string|max:500',
        ]);

        $incident->devis_statut = 'refuse';
        $incident->refus_note   = $request->refus_note;
        $incident->statut       = 'ouvert'; // Retour au début pour renégociation
        $incident->save();

        // Notifier tous les gestionnaires du refus
        $gestionnaires = \App\Models\User::where('role', 'gestionnaire')->get();
        foreach ($gestionnaires as $g) {
            $g->notifications()->create([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\Notifications\DevisRefuse',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $g->id,
                'data'            => json_encode([
                    'message' => '❌ Le propriétaire a <strong>refusé</strong> le devis pour : <strong>' . $incident->titre . '</strong>. Motif : ' . $request->refus_note,
                    'url'     => route('incidents.show', $incident),
                ]),
            ]);
        }

        return redirect()->route('incidents.show', $incident)
                         ->with('success', 'Refus enregistré. Le gestionnaire va être notifié pour renégocier.');
    }
}