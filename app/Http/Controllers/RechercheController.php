<?php

namespace App\Http\Controllers;

use App\Models\UniteLocative;
use App\Models\DemandeLocation;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class RechercheController extends Controller
{
    public function index(Request $request)
    {
        $query = UniteLocative::with('bien.proprietaire.user')->where('statut', 'libre');

        if ($request->filled('prix_max')) {
            $query->where('prix_loyer', '<=', $request->prix_max);
        }

        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', $request->chambres);
        }

        $unites = $query->latest()->paginate(12);

        return view('recherche.index', compact('unites'));
    }

    public function show(UniteLocative $unite)
    {
        $unite->load('bien.proprietaire.user');
        return view('recherche.show', compact('unite'));
    }

    public function postuler(Request $request, UniteLocative $unite)
    {
        $user = Auth::user();

        // Vérifier si le locataire a déjà postulé pour cette unité
        $dejaPostule = DemandeLocation::where('unite_locative_id', $unite->id)
            ->where('user_id', $user->id)
            ->whereIn('statut', ['en_attente', 'valide_proprietaire', 'valide_admin', 'accepte'])
            ->exists();

        if ($dejaPostule) {
            return back()->with('error', 'Vous avez déjà une demande en cours pour ce logement.');
        }

        $demande = DemandeLocation::create([
            'unite_locative_id' => $unite->id,
            'user_id' => $user->id,
            'statut' => 'en_attente',
            'message' => $request->message,
            'is_new' => true,
        ]);

        // Notifications
        $admins = User::where('role', 'admin')->get();
        $proprietaire = $unite->bien->proprietaire->user;

        $msg = "📢 Nouvelle Demande : **{$user->name}** souhaite louer l'unité **{$unite->libelle}** du bien **{$unite->bien->libelle}**.";
        
        Notification::send($admins, new \App\Notifications\ProfileUpdated($user, $msg));
        $proprietaire->notify(new \App\Notifications\ProfileUpdated($user, $msg));

        // Logger l'action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'creation',
            'description' => "Le locataire {$user->name} a postulé pour l'unité {$unite->libelle}.",
        ]);
        
        return redirect()->route('recherche.index')->with('success', 'Votre demande a été envoyée avec succès. Le propriétaire et l\'administrateur ont été informés.');
    }
}
