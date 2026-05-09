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
        $selectedYear = $request->get('year', session('selected_year', date('Y')));
        $selectedMonth = $request->get('month');

        $query = Paiement::with('contrat.locataire', 'contrat.bien')
                        ->whereYear('mois_concerne', $selectedYear);

        if (auth()->user()->isAdmin() && $selectedMonth) {
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

        return view('paiements.index', compact('paiements', 'selectedYear', 'selectedMonth', 'months'));
    }

    public function create()
    {
        $this->authorizeEditor();
        $user = auth()->user();
        
        $query = Contrat::where('statut', 'actif')->with('locataire', 'bien');
        
        if ($user->role === 'locataire' && $user->locataire) {
            $query->where('locataire_id', $user->locataire->id);
        } elseif ($user->role === 'proprietaire' && $user->proprietaire) {
            $query->whereHas('bien', fn($q) => $q->where('proprietaire_id', $user->proprietaire->id));
        }

        $contrats = $query->get();
        return view('paiements.create', compact('contrats'));
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
        $montantParMois = ($typePaiement === 'annuel') ? ($validated['montant'] / 12) : $validated['montant'];

        $firstPaiement = null;

        for ($i = 0; $i < $iterations; $i++) {
            $mois = Carbon::parse($validated['mois_concerne'])->addMonths($i);
            
            $data = $validated;
            unset($data['preuve_paiement']); // On le gère séparément
            $data['mois_concerne'] = $mois->format('Y-m-d');
            $data['montant'] = $montantParMois;
            $data['created_by'] = auth()->id();
            $data['preuve_paiement'] = $preuvePath;
            
            if (auth()->user()->role === 'locataire') {
                $data['statut'] = 'en_attente'; // Le locataire déclare, l'admin doit valider
            } else {
                if ($montantParMois >= $contrat->loyer) {
                    $data['statut'] = 'paye';
                } else {
                    $data['statut'] = 'partiel';
                }
            }

            if ($typePaiement === 'annuel') {
                $data['notes'] = ($data['notes'] ?? '') . " [Paiement Annuel - Mois " . ($i + 1) . "/12]";
            }

            $paiement = Paiement::updateOrCreate(
                [
                    'contrat_id' => $data['contrat_id'],
                    'mois_concerne' => $data['mois_concerne'],
                ],
                $data
            );
            if ($i === 0) $firstPaiement = $paiement;

            if ($data['statut'] === 'paye') {
                $count = Quittance::count() + 1;
                $quittance = Quittance::updateOrCreate(
                    ['paiement_id' => $paiement->id],
                    ['numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year]
                );

                // Notification au locataire
                $paiement->contrat->locataire->user->notify(new \App\Notifications\PaymentConfirmationNotification($paiement));

                // Log d'activité
                \App\Models\ActivityLog::log('paiement', "Encaissement de loyer pour {$paiement->contrat->locataire->prenom} {$paiement->contrat->locataire->nom} - Montant: " . number_format($paiement->montant, 0, ',', ' ') . " GNF", $paiement);
            }
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
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Seul l\'administrateur peut valider un paiement.');
            }

            $paiement->update(['statut' => 'paye']);

            // Génération de la quittance
            $count = Quittance::count() + 1;
            $quittance = Quittance::updateOrCreate(
                ['paiement_id' => $paiement->id],
                ['numero_quittance' => 'Q' . str_pad($count, 4, '0', STR_PAD_LEFT) . '-' . now()->year]
            );

            // Notification au locataire
            $paiement->contrat->locataire->user->notify(new \App\Notifications\PaymentConfirmationNotification($paiement));

            // Log d'activité
            \App\Models\ActivityLog::log('paiement', "Validation du paiement pour {$paiement->contrat->locataire->prenom} {$paiement->contrat->locataire->nom} (ID: {$paiement->reference})", $paiement);

            return redirect()->route('paiements.show', $paiement)->with('success', 'Paiement validé avec succès. La quittance a été générée.');
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