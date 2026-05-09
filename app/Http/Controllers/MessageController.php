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
        $rules = [
            'content'     => 'required|string|min:2',
            'attachment'  => 'nullable|file|max:10240', // 10MB
            'is_broadcast' => 'nullable|boolean',
            'broadcast_to' => 'nullable|string|in:all_owners,all_tenants,all_managers',
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
            
            // Validation WhatsApp-style
            $isPhoto = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isDoc = in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv']);

            if (!$isPhoto && !$isDoc) {
                return back()->withErrors(['attachment' => 'Format de fichier non supporté (Photos ou Documents uniquement).']);
            }

            $attachmentType = $isPhoto ? 'photo' : 'document';
            $folder = $isPhoto ? 'messages/photos' : 'messages/documents';
            $attachmentPath = $file->store($folder, 'public');
        }

        $sender = Auth::user();
        $isUrgent = $request->boolean('is_urgent');
        $type = $isUrgent ? 'urgent' : 'standard';

        // Destinataires
        $recipients = collect();
        if ($request->boolean('is_broadcast') && $sender->role === 'admin') {
            $roleMap = [
                'all_owners'   => 'proprietaire',
                'all_tenants'  => 'locataire',
                'all_managers' => 'gestionnaire',
            ];
            $targetRole = $roleMap[$request->broadcast_to];
            $recipients = User::where('role', $targetRole)->get();
        } else {
            $recipients->push(User::findOrFail($request->receiver_id));
        }

        foreach ($recipients as $recipient) {
            // SÉCURITÉ STRICTE
            if ($sender->role !== 'admin' && $recipient->role === 'admin' && !$request->boolean('is_support')) {
                continue; // Skip or handle error
            }

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

            // Intégration Document
            if ($attachmentPath) {
                \App\Models\Document::create([
                    'documentable_type' => get_class($recipient),
                    'documentable_id'   => $recipient->id,
                    'nom'               => $attachmentName,
                    'type'              => $attachmentType,
                    'chemin'            => $attachmentPath,
                    'taille_ko'         => round(filesize(storage_path('app/public/' . $attachmentPath)) / 1024),
                    'uploaded_by'       => $sender->id,
                ]);
            }

            // Notification
            $notifTitle = $sender->role === 'admin' ? "L'Administration" : $sender->name;
            $notifMsg = $attachmentPath 
                ? "Admin vous a envoyé un document/photo. Veuillez consulter vos documents."
                : "Nouveau message de " . $notifTitle;
            
            $recipient->notify(new \App\Notifications\ProfileUpdated($sender, $notifMsg));
        }

        return redirect()->route('messages.index')->with('success', 'Message(s) envoyé(s) avec succès.');
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
