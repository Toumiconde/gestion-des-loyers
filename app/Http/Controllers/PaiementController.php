<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Contrat;
use App\Models\Quittance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaiementController extends Controller
{
    private function authorizeEditor()
    {
        // On permet aux locataires d'enregistrer leurs paiements
        // Mais ils ne peuvent pas tout faire (ex: supprimer)
        if (!auth()->check()) {
            abort(403, 'Accès non autorisé - PaiementCtrl.');
        }
    }

    public function index(Request $request)
    {
        if ($request->has('year')) {
            session(['selected_year' => $request->get('year')]);
        }
        if ($request->has('month')) {
            session(['selected_month' => $request->get('month')]);
        }

        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');

        // Paiements en attente (toutes années) — toujours visibles pour admin/comptable/gestionnaire
        $paiementsEnAttente = collect();
        if (in_array(auth()->user()->role, ['admin', 'comptable', 'gestionnaire'])) {
            $paiementsEnAttente = Paiement::with('contrat.locataire', 'contrat.bien')
                ->where('statut', 'en_attente')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $query = Paiement::with('contrat.locataire', 'contrat.bien')
                        ->whereYear('mois_concerne', $selectedYear);

        if (in_array(auth()->user()->role, ['admin', 'comptable', 'gestionnaire']) && $selectedMonth) {
            $query->whereMonth('mois_concerne', $selectedMonth);
        }

        if (auth()->user()->isProprietaire()) {
            if (!auth()->user()->proprietaire) {
                abort(403, 'Profil propriétaire non configuré.');
            }
            $proprietaireId = auth()->user()->proprietaire->id;
            $query->whereHas('contrat.bien', function($q) use ($proprietaireId) {
                $q->where('proprietaire_id', $proprietaireId);
            });
        } elseif (auth()->user()->isLocataire()) {
            $locataireId = auth()->user()->locataire->id ?? 0;
            $query->whereHas('contrat', function($q) use ($locataireId) {
                $q->where('locataire_id', $locataireId);
            });
        }

        $paiements = $query->paginate(15);
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('paiements.index', compact('paiements', 'paiementsEnAttente', 'selectedYear', 'selectedMonth', 'months'));
    }

    public function create(Request $request)
    {
        $this->authorizeEditor();
        $user = auth()->user();
        
        $query = Contrat::whereIn('statut', ['actif', 'brouillon'])->with('locataire', 'bien');
        
        if ($user->role === 'locataire' && $user->locataire) {
            $query->where('locataire_id', $user->locataire->id);
        } elseif ($user->role === 'proprietaire' && $user->proprietaire) {
            $query->whereHas('bien', fn($q) => $q->where('proprietaire_id', $user->proprietaire->id));
        }

        $contrats = $query->get();

        // Calcul du mois par défaut
        $defaultMonth = date('Y-m-01');
        $paiementPartielExistant = null;
        
        $contratPourDefaut = null;
        if ($request->has('contrat_id')) {
            $contratPourDefaut = Contrat::find($request->contrat_id);
        } elseif ($contrats->count() === 1) {
            $contratPourDefaut = $contrats->first();
        }
        
        if ($contratPourDefaut) {
            // PRIORITÉ 1 : Y a-t-il un paiement partiel/en_attente non soldé ?
            // Si oui, le locataire DOIT compléter CE mois-là en premier.
            $paiementPartielExistant = Paiement::where('contrat_id', $contratPourDefaut->id)
                ->whereIn('statut', ['partiel', 'en_attente'])
                ->where('solde_restant', '>', 0)
                ->orderBy('mois_concerne', 'asc') // Le plus ancien d'abord
                ->first();

            if ($paiementPartielExistant) {
                // Forcer le mois sur le mois du paiement partiel existant
                $defaultMonth = \Carbon\Carbon::parse($paiementPartielExistant->mois_concerne)->format('Y-m-01');
            } else {
                // PRIORITÉ 2 : Calculer le prochain mois dû normalement
                $dernierPaiementValide = $contratPourDefaut->paiements()
                    ->where('statut', 'paye')
                    ->orderBy('mois_concerne', 'desc')
                    ->first();
                
                if ($dernierPaiementValide) {
                    $defaultMonth = \Carbon\Carbon::parse($dernierPaiementValide->mois_concerne)->addMonth()->format('Y-m-01');
                } else {
                    $defaultMonth = \Carbon\Carbon::parse($contratPourDefaut->date_debut)->format('Y-m-01');
                }
            }
        }

        return view('paiements.create', compact('contrats', 'defaultMonth', 'paiementPartielExistant'));
    }

    public function store(Request $request)
    {
        $this->authorizeEditor();
        $validated = $request->validate([
            'contrat_id'     => 'required|exists:contrats,id',
            'mois_concerne'  => 'required|date',
            'montant'        => 'required|numeric|min:1',
            'date_paiement'  => 'required|date',
            'mode_reglement' => 'required|in:especes,virement,mobile_money,cheque,autre',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'preuve_paiement' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $contrat = Contrat::findOrFail($validated['contrat_id']);

        // Sécurité : Un locataire ne peut payer que pour son propre contrat
        if (auth()->user()->role === 'locataire' && $contrat->locataire_id !== auth()->user()->locataire->id) {
            abort(403, 'Action non autorisée sur ce contrat.');
        }

        // Gestion de la preuve de paiement
        $preuvePath = null;
        if ($request->hasFile('preuve_paiement')) {
            $preuvePath = $request->file('preuve_paiement')->store('paiements/preuves', 'public');
        }

        $typePaiement = $request->input('type_paiement', 'mensuel');
        $iterations = ($typePaiement === 'annuel') ? 12 : 1;
        $montantSaisi = ($typePaiement === 'annuel') ? ($validated['montant'] / 12) : $validated['montant'];

        $firstPaiement = null;

        for ($i = 0; $i < $iterations; $i++) {
            $mois = Carbon::parse($validated['mois_concerne'])->addMonths($i)->startOfMonth();
            // ⚠️ TOUJOURS normaliser au 1er du mois pour garantir la correspondance entre versements
            $moisFormate = $mois->format('Y-m-01');
            $loyerAttendu = $contrat->loyer;

            // =========================================================
            // LOGIQUE SOLDE REPORT : reporter le solde du mois précédent
            // =========================================================
            $moisPrecedent = $mois->copy()->subMonth()->format('Y-m-01');
            $paiementMoisPrecedent = Paiement::where('contrat_id', $contrat->id)
                ->whereYear('mois_concerne', Carbon::parse($moisPrecedent)->year)
                ->whereMonth('mois_concerne', Carbon::parse($moisPrecedent)->month)
                ->whereIn('statut', ['partiel', 'en_attente'])
                ->first();

            $soldeReporte = 0;
            if ($paiementMoisPrecedent && $paiementMoisPrecedent->solde_restant > 0) {
                $soldeReporte = $paiementMoisPrecedent->solde_restant;
                $loyerAttendu += $soldeReporte;
            }

            // =========================================================
            // CUMUL DES VERSEMENTS : chercher un paiement existant pour ce mois
            // Utiliser whereYear + whereMonth pour éviter les problèmes de format de date
            // =========================================================
            $paiementExistant = Paiement::where('contrat_id', $contrat->id)
                ->whereYear('mois_concerne', $mois->year)
                ->whereMonth('mois_concerne', $mois->month)
                ->first();

            $totalVerse = $montantSaisi;
            if ($paiementExistant) {
                $totalVerse = $paiementExistant->total_verse + $montantSaisi;
            }

            $soldeRestant = max(0, $loyerAttendu - $totalVerse);

            // Détermination du statut
            if (auth()->user()->role === 'locataire') {
                $statut = 'en_attente'; // Toujours en attente pour validation comptable
            } else {
                $statut = ($soldeRestant <= 0) ? 'paye' : 'partiel';
            }

            // Préparation des données
            $data = [
                'contrat_id'      => $contrat->id,
                'mois_concerne'   => $moisFormate,
                'montant'         => $montantSaisi,
                'total_verse'     => $totalVerse,
                'loyer_attendu'   => $loyerAttendu,
                'solde_restant'   => $soldeRestant,
                'date_paiement'   => $validated['date_paiement'],
                'mode_reglement'  => $validated['mode_reglement'],
                'reference'       => $validated['reference'] ?? null,
                'notes'           => $validated['notes'] ?? null,
                'created_by'      => auth()->id(),
                'preuve_paiement' => $preuvePath ?? ($paiementExistant?->preuve_paiement),
                'statut'          => $statut,
            ];

            if ($typePaiement === 'annuel') {
                $data['notes'] = ($data['notes'] ?? '') . " [Paiement Annuel - Mois " . ($i + 1) . "/12]";
            }

            // Mise à jour cumulative (on ne remplace pas, on accumule)
            if ($paiementExistant) {
                $paiementExistant->update($data);
                $paiement = $paiementExistant->fresh();
            } else {
                $paiement = Paiement::create($data);
            }

            if ($i === 0) $firstPaiement = $paiement;

            // =========================================================
            // NOTIFICATIONS LOCATAIRE selon le solde
            // =========================================================
            if (auth()->user()->role === 'locataire' && $i === 0) {
                $moisLabel = $mois->locale('fr')->isoFormat('MMMM YYYY');
                $staff = \App\Models\User::whereIn('role', ['admin', 'gestionnaire', 'comptable'])->get();

                if ($soldeRestant <= 0) {
                    // Loyer complet — notifier le staff
                    \Illuminate\Support\Facades\Notification::send($staff, new \App\Notifications\ProfileUpdated(
                        auth()->user(),
                        "💰 Versement complet reçu de **{$contrat->locataire->nom_complet}** pour **{$moisLabel}** : " . number_format($totalVerse, 0, ',', ' ') . " GNF. À valider."
                    ));
                } else {
                    // Versement partiel — notifier le staff
                    \Illuminate\Support\Facades\Notification::send($staff, new \App\Notifications\ProfileUpdated(
                        auth()->user(),
                        "⚠️ Versement partiel de **{$contrat->locataire->nom_complet}** pour **{$moisLabel}** : " . number_format($totalVerse, 0, ',', ' ') . " GNF versés. Reste : " . number_format($soldeRestant, 0, ',', ' ') . " GNF."
                    ));
                }
            }

            // Génération de quittance si paye (cas admin/gestionnaire direct)
            if ($statut === 'paye') {
                $count = Quittance::count() + 1;
                Quittance::updateOrCreate(
                    ['paiement_id' => $paiement->id],
                    ['numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year]
                );
                if ($paiement->contrat->bien->proprietaire && $paiement->contrat->bien->proprietaire->user) {
                    $paiement->contrat->bien->proprietaire->user->notify(new \App\Notifications\OwnerPaymentReceivedNotification($paiement));
                }
                \App\Models\ActivityLog::log('paiement', "Encaissement de loyer pour {$paiement->contrat->locataire->prenom} {$paiement->contrat->locataire->nom} - Montant: " . number_format($paiement->montant, 0, ',', ' ') . " GNF", $paiement);
            }

            // Activation automatique du contrat
            if ($statut === 'paye' && $paiement->contrat->statut === 'brouillon') {
                $paiement->contrat->update(['statut' => 'actif']);
            }
        }

        // Message de retour adapté
        if (auth()->user()->isLocataire() && $firstPaiement) {
            $moisLabel = Carbon::parse($firstPaiement->mois_concerne)->locale('fr')->isoFormat('MMMM YYYY');
            $solde = $firstPaiement->solde_restant;
            if ($solde <= 0) {
                $msg = "✅ Loyer de **{$moisLabel}** intégralement versé ({$firstPaiement->total_verse} GNF) ! En attente de validation par le comptable.";
            } else {
                $msg = "✅ Versement enregistré pour **{$moisLabel}**. Il vous reste **" . number_format($solde, 0, ',', ' ') . " GNF** à payer pour ce mois. Le comptable est notifié.";
            }
            return redirect()->route('paiements.index')->with('success', $msg);
        }

        if ($typePaiement === 'mensuel' && $firstPaiement && $firstPaiement->statut === 'paye' && $firstPaiement->quittance) {
            return redirect()->route('quittances.show', $firstPaiement->quittance->id)
                             ->with('success', 'Paiement enregistré avec succès. Voici la quittance.');
        }

        return redirect()->route('paiements.index')
                         ->with('success', ($typePaiement === 'annuel' ? '12 paiements mensuels enregistrés.' : 'Paiement enregistré avec succès.'));
    }

    public function show(Paiement $paiement)
    {
        if (auth()->user()->isProprietaire() && auth()->user()->proprietaire) {
            if ($paiement->contrat->bien->proprietaire_id !== auth()->user()->proprietaire->id) {
                abort(403, 'Ce paiement ne vous concerne pas.');
            }
        } elseif (auth()->user()->isLocataire() && auth()->user()->locataire) {
            if ($paiement->contrat->locataire_id !== auth()->user()->locataire->id) {
                abort(403, 'Ce paiement ne vous concerne pas.');
            }
        }

        if ($paiement->statut === 'paye' && !$paiement->quittance) {
            $count = \App\Models\Quittance::count() + 1;
            $paiement->quittance()->create([
                'numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year
            ]);
            $paiement->refresh();
        }

        $paiement->load('contrat.locataire', 'contrat.bien', 'quittance');
        return view('paiements.show', compact('paiement'));
    }

    // On ne modifie pas un paiement (règle RP-05)
    public function edit(Paiement $paiement)
    {
        abort(403, 'Un paiement enregistré ne peut pas être modifié.');
    }

    public function update(Request $request, Paiement $paiement)
    {
        $this->authorizeEditor();
        
        if ($request->input('action') === 'valider') {
            if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire() && !auth()->user()->isComptable()) {
                abort(403, 'Seul l\'administrateur, le gestionnaire ou le comptable peut valider un paiement.');
            }

            if (str_contains($paiement->notes, '[Paiement Annuel')) {
                // Trouver les 12 mois liés (même date de soumission, même contrat, en attente, et avec la mention Paiement Annuel)
                $relatedPaiements = \App\Models\Paiement::where('contrat_id', $paiement->contrat_id)
                                            ->where('date_paiement', $paiement->date_paiement)
                                            ->where('statut', 'en_attente')
                                            ->where('notes', 'like', '%[Paiement Annuel%')
                                            ->get();

                foreach($relatedPaiements as $related) {
                    $related->update(['statut' => 'paye', 'valide_par' => auth()->id()]);
                    $c = \App\Models\Quittance::count() + 1;
                    \App\Models\Quittance::updateOrCreate(
                        ['paiement_id' => $related->id],
                        ['numero_quittance' => 'Q' . str_pad($c, 4, '0', STR_PAD_LEFT) . '-' . now()->year]
                    );
                }
                $quittance = $paiement->refresh()->quittance;
            } else {
                $paiement->update(['statut' => 'paye', 'valide_par' => auth()->id()]);
                
                // Génération de la quittance unique
                $count = \App\Models\Quittance::count() + 1;
                $quittance = \App\Models\Quittance::updateOrCreate(
                    ['paiement_id' => $paiement->id],
                    ['numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year]
                );
            }

            // Notification au locataire
            $paiement->contrat->locataire->user->notify(new \App\Notifications\PaymentConfirmationNotification($paiement));

            // Notification au propriétaire
            if ($paiement->contrat->bien->proprietaire && $paiement->contrat->bien->proprietaire->user) {
                $paiement->contrat->bien->proprietaire->user->notify(new \App\Notifications\OwnerPaymentReceivedNotification($paiement));
            }

            // Log d'activité
            \App\Models\ActivityLog::log('paiement', "Validation du paiement pour {$paiement->contrat->locataire->prenom} {$paiement->contrat->locataire->nom} (ID: {$paiement->reference})", $paiement);

            // Activation automatique du contrat si c'était le premier paiement (brouillon)
            if ($paiement->contrat->statut === 'brouillon') {
                $paiement->contrat->update(['statut' => 'actif']);
            }

            return redirect()->route('quittances.show', $quittance->id)->with('success', 'Paiement validé avec succès. La quittance a été générée.');
        }

        abort(403, 'Action non reconnue.');
    }

    public function relancer(Paiement $paiement)
    {
        $locataireUser = $paiement->contrat->locataire->user;

        if (!$locataireUser) {
            return back()->with('error', 'Le locataire n\'a pas de compte utilisateur associé.');
        }

        // Déclencher la notification de rappel
        $locataireUser->notify(new \App\Notifications\PaymentReminderNotification($paiement->contrat, $paiement->contrat->loyer));

        // Créer un message officiel (noreply) dans la boîte de réception
        \App\Models\Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $locataireUser->id,
            'content'     => "RAPPEL DE PAIEMENT : Votre loyer pour le bien « " . $paiement->contrat->bien->libelle . " » concernant le mois de " . $paiement->mois_concerne->format('F Y') . " est en attente de régularisation. Montant dû : " . number_format($paiement->montant, 0, ',', ' ') . " GNF.",
            'type'        => 'urgent',
            'is_urgent'   => true,
            'can_reply'   => false,
        ]);

        return back()->with('success', 'Relance envoyée avec succès à ' . $locataireUser->name);
    }

    public function destroy(Paiement $paiement)
    {
        abort(403, 'Un paiement ne peut pas être supprimé.');
    }
}