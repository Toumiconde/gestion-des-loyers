<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Paiement;
use App\Models\Locataire;
use App\Models\Contrat;
use App\Notifications\BroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BroadcastController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'locataire') abort(403, 'Action non autorisée pour les locataires.');
        return view('broadcast.index', compact('user'));
    }

    public function send(Request $request)
    {
        if (Auth::user()->role === 'locataire') abort(403);
        
        $request->validate([
            'target' => 'required|string',
            'subject' => 'required|string|max:200',
            'content' => 'required|string',
            'channel' => 'required|array', // ['internal', 'email', 'sms']
        ]);

        $sender = Auth::user();
        $recipients = collect();

        // 1. Détermination des destinataires
        switch ($request->target) {
            case 'all_tenants':
                if ($sender->isAdmin()) {
                    $recipients = User::where('role', 'locataire')->get();
                } elseif ($sender->isProprietaire()) {
                    $recipients = User::whereHas('locataire.contrats.bien', function($q) use ($sender) {
                        $q->where('proprietaire_id', $sender->proprietaire->id);
                    })->get();
                }
                break;

            case 'all_owners':
                if ($sender->isAdmin()) {
                    $recipients = User::where('role', 'proprietaire')->get();
                }
                break;

            case 'unpaid_tenants':
                // Locataires qui n'ont pas payé le mois en cours
                $currentMonth = date('Y-m-01');
                $paidContractIds = Paiement::where('mois_concerne', $currentMonth)
                    ->where('statut', 'paye')
                    ->pluck('contrat_id');

                $query = User::where('role', 'locataire')
                    ->whereHas('locataire.contrats', function($q) use ($paidContractIds) {
                        $q->where('statut', 'actif')->whereNotIn('id', $paidContractIds);
                    });

                if ($sender->isProprietaire()) {
                    $query->whereHas('locataire.contrats.bien', function($q) use ($sender) {
                        $q->where('proprietaire_id', $sender->proprietaire->id);
                    });
                }
                
                $recipients = $query->get();
                break;
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Aucun destinataire trouvé pour ce groupe.');
        }

        // 2. Envoi sur les canaux sélectionnés
        foreach ($recipients as $recipient) {
            $isUrgent = ($request->target === 'unpaid_tenants');

            if (in_array('internal', $request->channel)) {
                Message::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $recipient->id,
                    'subject' => $request->subject,
                    'content' => $request->content,
                    'is_urgent' => $isUrgent,
                ]);
            }

            // Notifier le destinataire qu'il a un nouveau message en attente
            $recipient->notify(new BroadcastNotification($request->subject, $request->content, $isUrgent));
            
            // Simuler l'envoi Email/SMS ici
        }

        return redirect()->route('messages.index')->with('success', 'Message de diffusion envoyé avec succès à ' . $recipients->count() . ' destinataires.');
    }
}
