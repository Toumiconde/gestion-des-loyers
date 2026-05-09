<?php

namespace App\Http\Controllers;

use App\Models\DemandeLocation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DemandeLocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = DemandeLocation::with(['uniteLocative.bien.proprietaire.user', 'uniteLocative.bien.documents', 'user']);

        if ($user->role === 'proprietaire') {
            $proprietaireId = $user->proprietaire->id;
            $query->whereHas('uniteLocative.bien', function($q) use ($proprietaireId) {
                $q->where('proprietaire_id', $proprietaireId);
            });
        } elseif ($user->role === 'locataire') {
            $query->where('user_id', $user->id);
        }
        // Admin voit tout

        $demandes = $query->latest()->paginate(15);

        // Marquer comme lu pour l'utilisateur actuel
        if ($user->role === 'proprietaire') {
            DemandeLocation::where('is_new', true)
                ->whereHas('uniteLocative.bien', function($q) use ($user) {
                    $q->where('proprietaire_id', $user->proprietaire->id);
                })->update(['is_new' => false]);
        } elseif ($user->role === 'admin') {
            DemandeLocation::where('is_new', true)->update(['is_new' => false]);
        }

        return view('demandes-location.index', compact('demandes'));
    }

    public function validerProprietaire(DemandeLocation $demande)
    {
        if (Auth::user()->role !== 'proprietaire' && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $demande->update(['statut' => 'valide_proprietaire']);

        // Notification à l'Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ProfileUpdated(Auth::user(), "✅ Demande de location #{$demande->id} validée par le propriétaire **{$demande->uniteLocative->bien->proprietaire->user->name}**. Vous pouvez maintenant donner l'accord final."));

        // Message au locataire
        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $demande->user_id,
            'subject' => "Validation de votre demande - " . $demande->uniteLocative->bien->libelle,
            'content' => "Bonne nouvelle ! Votre propriétaire a validé votre demande pour le logement : **{$demande->uniteLocative->bien->libelle}**. L'administrateur va maintenant effectuer la validation finale pour vous permettre de procéder au paiement.",
            'is_urgent' => true
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'modification',
            'description' => "Le propriétaire a validé la demande #{$demande->id}.",
        ]);

        return back()->with('success', 'Demande validée par le propriétaire. L\'administrateur et le locataire ont été notifiés.');
    }

    public function validerAdmin(DemandeLocation $demande)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $prevStatus = $demande->statut;
        $demande->update(['statut' => 'accepte']); 

        // Marquer l'unité comme réservée
        $demande->uniteLocative->update(['statut' => 'reserve']);

        // Créer automatiquement un projet de contrat
        $contrat = \App\Models\Contrat::create([
            'numero_contrat' => 'CONTRAT-' . strtoupper(uniqid()),
            'bien_id' => $demande->uniteLocative->bien_id,
            'unite_locative_id' => $demande->uniteLocative->id,
            'locataire_id' => $demande->user->locataire->id ?? \App\Models\Locataire::firstOrCreate(['user_id' => $demande->user->id], [
                'nom' => $demande->user->name,
                'email' => $demande->user->email
            ])->id,
            'date_debut' => now(),
            'loyer' => $demande->uniteLocative->prix_loyer,
            'depot_garantie' => 0,
            'statut' => 'brouillon',
        ]);

        // Message au locataire
        $messageContent = "Félicitations ! Votre demande pour le logement **{$demande->uniteLocative->bien->libelle}** a été acceptée par l'administration.";
        
        if ($prevStatus === 'valide_proprietaire') {
            $messageContent .= "\n\nGrâce à l'accord préalable de votre propriétaire, l'administrateur vous a officiellement autorisé à intégrer votre nouvelle maison. Veuillez procéder au paiement du premier loyer pour finaliser votre emménagement.";
        } else {
            $messageContent .= "\n\nVeuillez procéder au paiement du premier loyer pour finaliser votre emménagement.";
        }

        \App\Models\Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $demande->user_id,
            'subject' => "Félicitations ! Votre demande est acceptée",
            'content' => $messageContent,
            'is_urgent' => true
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'modification',
            'description' => "L'administrateur a validé la demande #{$demande->id}.",
        ]);

        // Envoyer l'email au locataire
        $this->envoyerEmailAcceptation($demande);

        return back()->with('success', 'Demande validée par l\'administration. Le locataire a reçu un message pour procéder au paiement.');
    }

    public function rejeter(DemandeLocation $demande)
    {
        $demande->update(['statut' => 'rejete']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'modification',
            'description' => "La demande #{$demande->id} a été rejetée.",
        ]);

        return back()->with('error', 'La demande a été rejetée.');
    }

    protected function envoyerEmailAcceptation(DemandeLocation $demande)
    {
        $locataire = $demande->user;
        
        try {
            Mail::to($locataire->email)->send(new \App\Mail\DemandeAcceptee($demande));
        } catch (\Exception $e) {
            // Log error if mail fails but don't stop the process
            \Log::error("Échec de l'envoi de l'email à {$locataire->email} : " . $e->getMessage());
        }
    }
}
