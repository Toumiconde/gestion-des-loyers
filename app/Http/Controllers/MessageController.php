<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use App\Notifications\NewSupportRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Message::with(['sender', 'receiver'])->orderBy('created_at', 'desc');

        if ($request->get('filter') === 'support') {
            if ($user->role === 'comptable') {
                abort(403, 'Accès non autorisé au support technique.');
            }
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

            // Pour le comptable, on exclut TOUJOURS le support technique de la liste générale
            if ($user->role === 'comptable') {
                $query->where('is_support', false);
            }
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

        if ($user->role === 'locataire' || $user->role === 'proprietaire') {
            // Locataires et Propriétaires écrivent UNIQUEMENT au staff (Gestionnaire/Comptable)
            $receivers = User::whereIn('role', ['gestionnaire', 'comptable'])
                             ->where('id', '!=', $user->id)
                             ->get();
        } else {
            // L'agence (Admin/Gestionnaire) peut écrire à tout le monde
            $receivers = User::where('id', '!=', $user->id)->get();
        }

        return view('messages.create', compact('receivers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'content'     => 'required|string|min:2',
            'attachment'  => 'nullable|file|max:10240',
            'is_broadcast' => 'nullable|boolean',
            'broadcast_to' => 'required_if:is_broadcast,1|string|in:all_owners,all_tenants,all_managers,tenants_in_debt',
        ];

        if (!$request->boolean('is_broadcast')) {
            $rules['receiver_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->getClientOriginalExtension());
            $attachmentName = $file->getClientOriginalName();
            
            $isPhoto = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isDoc = in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv']);

            if (!$isPhoto && !$isDoc) {
                return back()->withErrors(['attachment' => 'Format non supporté.']);
            }

            $attachmentType = $isPhoto ? 'photo' : 'document';
            $attachmentPath = $file->store($isPhoto ? 'messages/photos' : 'messages/documents', 'public');
        }

        $sender = Auth::user();
        $isUrgent = $request->boolean('is_urgent');
        $type = $isUrgent ? 'urgent' : 'standard';

        // Destinataires
        $recipients = collect();
        if ($request->boolean('is_broadcast')) {
            if ($request->broadcast_to === 'tenants_in_debt') {
                // Locataires en dette ou partiel
                $locataireIds = \App\Models\Paiement::whereIn('statut', ['partiel', 'impayé', 'en_attente'])
                                ->pluck('contrat_id')
                                ->unique();
                
                $query = User::where('role', 'locataire')
                    ->whereHas('locataire.contrats', function($q) use ($locataireIds) {
                        $q->whereIn('id', $locataireIds);
                    });
                
                // Si c'est un propriétaire qui envoie, il ne voit que SES locataires en dette
                if ($sender->role === 'proprietaire') {
                    $query->whereHas('locataire.contrats.bien', function($q) use ($sender) {
                        $q->where('proprietaire_id', $sender->proprietaire->id);
                    });
                }
                $recipients = $query->get();
            } else if ($sender->role === 'admin' || $sender->role === 'gestionnaire') {
                $roleMap = [
                    'all_owners'   => 'proprietaire',
                    'all_tenants'  => 'locataire',
                    'all_managers' => 'gestionnaire',
                ];
                $recipients = User::where('role', $roleMap[$request->broadcast_to] ?? 'locataire')->get();
            }
        } else {
            $recipients->push(User::findOrFail($request->receiver_id));
        }

        foreach ($recipients as $recipient) {
            // SÉCURITÉ : Ne pas s'envoyer à soi-même
            if ($recipient->id === $sender->id) continue;

            $message = Message::create([
                'sender_id'       => $sender->id,
                'receiver_id'     => $recipient->id,
                'broadcast_to'    => $request->broadcast_to,
                'content'         => $request->content,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'is_urgent'       => $isUrgent,
                'type'            => $type,
                'is_support'      => $request->boolean('is_support'),
            ]);

            if ($attachmentPath) {
                \App\Models\Document::create([
                    'documentable_type' => 'App\\Models\\User',
                    'documentable_id'   => $recipient->id,
                    'nom'               => $attachmentName,
                    'type'              => $attachmentType,
                    'chemin'            => $attachmentPath,
                    'taille_ko'         => round(Storage::disk('public')->size($attachmentPath) / 1024),
                    'uploaded_by'       => $sender->id,
                ]);
            }

            $notifMsg = $attachmentPath 
                ? "Admin/Gestion vous a envoyé un document. Consultez vos documents."
                : "Nouveau message de " . $sender->name;
            
            $recipient->notify(new \App\Notifications\ProfileUpdated($sender, $notifMsg));
        }

        $count = $recipients->count();
        if ($count === 0) {
            return back()->withErrors(['is_broadcast' => 'Aucun destinataire trouvé pour ce groupe.'])->withInput();
        }

        $successMsg = $count > 1 
            ? "Message de diffusion envoyé avec succès à {$count} destinataires."
            : "Message envoyé avec succès.";

        return redirect()->route('messages.index')->with('success', $successMsg);
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
