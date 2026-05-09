<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Affiche la page de choix du rôle (locataire ou propriétaire).
     * Si l'onboarding est déjà fait, on redirige vers le dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Si l'onboarding est déjà terminé, plus besoin d'y revenir
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index');
    }

    /**
     * Enregistre le choix du rôle, crée le profil correspondant,
     * et marque l'onboarding comme terminé.
     */
    public function selectRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:locataire,proprietaire',
        ]);

        $user = Auth::user();
        $role = $request->role;

        // 1. Supprimer l'ancienne fiche auto-créée si elle ne correspond pas au choix
        if ($role === 'proprietaire' && $user->locataire) {
            $user->locataire->delete(); // Supprimer la fiche locataire par défaut
        }

        // 2. Mettre à jour le rôle de l'utilisateur
        $user->update(['role' => $role]);

        // 3. Créer la fiche correspondante si elle n'existe pas encore
        if ($role === 'locataire' && !$user->locataire) {
            $nameParts = explode(' ', $user->name, 2);
            Locataire::create([
                'user_id' => $user->id,
                'nom'     => $nameParts[1] ?? $user->name,
                'prenom'  => $nameParts[0] ?? '',
                'email'   => $user->email,
            ]);
        } elseif ($role === 'proprietaire' && !$user->fresh()->proprietaire) {
            Proprietaire::create([
                'user_id' => $user->id,
            ]);
        }

        // 4. Marquer l'onboarding comme terminé → ne plus jamais afficher cette page
        $user->update(['onboarding_completed' => true]);

        $message = $role === 'locataire'
            ? '🏠 Bienvenue ! Commencez par rechercher le logement de votre choix.'
            : '🔑 Bienvenue ! Votre espace propriétaire est prêt.';

        if ($role === 'locataire') {
            return redirect()->route('recherche.index')->with('success', $message);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}
