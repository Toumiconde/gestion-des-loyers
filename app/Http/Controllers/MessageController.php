<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use App\Notifications\NewSupportRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Message::with(['sender', 'receiver'])->orderBy('created_at', 'desc');

        if ($request->get('filter') === 'support') {
            $query->where('is_support', true);
        }

        if ($request->get('filter') === 'unread') {
            $query->where('is_read', false)->where('receiver_id', $user->id);
        }

        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            });
        }

        $messages = $query->get();

        return view('messages.index', compact('messages'));
    }

    public function archived()
    {
        $user = Auth::user();
        $query = Message::onlyTrashed()->with(['sender', 'receiver'])->orderBy('deleted_at', 'desc');

        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            });
        }

        $messages = $query->get();
        return view('messages.archives', compact('messages'));
    }

    public function create()
    {
        $user = Auth::user();
        $receivers = collect();

        if ($user->role === 'locataire') {
            // Un locataire peut écrire à son (ou ses) proprio(s) UNIQUEMENT (plus d'admin)
            if ($user->locataire) {
                $ownerIds = $user->locataire->contrats->pluck('bien.proprietaire.user_id')->filter()->unique();
                $receivers = User::whereIn('id', $ownerIds)->get();
            }
        } else if ($user->role === 'proprietaire') {
            // Un proprio peut écrire à ses locataires UNIQUEMENT (plus d'admin)
            if ($user->proprietaire) {
                $locataireIds = $user->proprietaire->biens->flatMap->contrats->pluck('locataire.user_id')->filter();
                $receivers = User::whereIn('id', $locataireIds)->get();
            }
        } else {
            // Admin peut écrire à tout le monde
            $receivers = User::where('id', '!=', $user->id)->get();
        }

        return view('messages.create', compact('receivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required|string|min:5',
        ]);

        $content = $request->content;
        $isUrgent = $request->boolean('is_urgent');
        $type = $isUrgent ? 'urgent' : 'standard';

        // Analyse de mots clés
        if (!$isUrgent) {
            $urgentKeywords = ['quitter', 'départ', 'urgent', 'problème', 'panne', 'fuite', 'résiliation', 'argent', 'payement', 'dette', 'loyer'];
            foreach ($urgentKeywords as $word) {
                if (stripos($content, $word) !== false) {
                    $isUrgent = true;
                    $type = 'urgent';
                    break;
                }
            }
        }

        $receiver = User::findOrFail($request->receiver_id);
        
        // SÉCURITÉ STRICTE : Les locataires et proprios ne peuvent pas écrire à l'admin (sauf pour le Support)
        if (Auth::user()->role !== 'admin' && $receiver->role === 'admin' && !$request->boolean('is_support')) {
            abort(403, 'Vous n\'êtes pas autorisé à contacter l\'administration directement.');
        }

        // Sécurité role-based
        if (Auth::user()->role === 'locataire' && $receiver->role === 'locataire') {
            abort(403, 'Communication entre locataires non autorisée.');
        }

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $content,
            'is_urgent'   => $isUrgent,
            'type'        => $type,
            'is_support'  => $request->boolean('is_support'),
            'can_reply'   => true, // On autorise toujours la réponse pour permettre le fil de discussion
        ]);

        // Envoyer une notification au destinataire
        $receiver = User::find($request->receiver_id);
        
        if ($request->boolean('is_support') && $receiver->role === 'admin') {
            // Notification isolée pour le support (Admin)
            $receiver->notify(new \App\Notifications\NewSupportRequestNotification(Auth::user(), $content));
        } else {
            $senderName = (Auth::user()->role === 'admin' && $request->boolean('is_support')) ? 'Le Système' : Auth::user()->name;
            $receiver->notify(new ProfileUpdated(Auth::user(), "Nouveau message " . ($isUrgent ? 'URGENT' : '') . " de " . $senderName));
        }

        // Si le message n'implique pas déjà l'admin, on notifie l'admin aussi
        if (Auth::user()->role !== 'admin' && $receiver->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new ProfileUpdated(Auth::user(), "Copie Notification: " . Auth::user()->name . " a écrit à " . $receiver->name));
            }
        }

        if ($request->boolean('is_support')) {
            return redirect()->route('messages.index')->with('success', 'Votre requête support a été envoyée au système.');
        }

        return redirect()->route('messages.index')->with('success', 'Votre message a été envoyé.');
    }

    public function show(Message $message)
    {
        $user = Auth::user();

        // Sécurité : Seul l'admin, l'expéditeur ou le destinataire peut voir le message
        if ($user->role !== 'admin' && $message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Accès non autorisé à ce message.');
        }

        if ($message->receiver_id === $user->id) {
            $message->is_read = true;
            $message->save();
        }
        return view('messages.show', compact('message'));
    }

    public function markAllAsRead()
    {
        Message::where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return back()->with('success', 'Tous les messages ont été marqués comme lus.');
    }

    public function destroy(Message $message)
    {
        // Seul l'admin ou les participants peuvent supprimer
        if (auth()->id() !== $message->sender_id && auth()->id() !== $message->receiver_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message déplacé dans l\'historique.');
    }

    public function restore($id)
    {
        $message = Message::withTrashed()->findOrFail($id);
        $message->restore();
        return redirect()->route('messages.archived')->with('success', 'Message restauré.');
    }
}
