<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page d'authentification de Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Gère le retour de Google après l'authentification.
     * 
     * Logique :
     * - Nouvel utilisateur → créer compte, envoyer vers onboarding
     * - Utilisateur existant + onboarding non fait → envoyer vers onboarding
     * - Utilisateur existant + onboarding fait → dashboard
     * - Admin/gestionnaire/comptable → dashboard directement (ils ne passent pas par l'onboarding)
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // On cherche l'utilisateur par son google_id ou par son email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Utilisateur existant : on met à jour ses infos Google
                $user->update([
                    'google_id'            => $googleUser->id,
                    'avatar'               => $googleUser->avatar,
                    'google_token'         => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'email_verified_at'    => $user->email_verified_at ?? now(),
                ]);

                // LOG DE CONNEXION
                \App\Models\ActivityLog::log('connexion', 's\'est connecté via Google', $user);

            } else {
                // Nouvel utilisateur : on crée le compte avec un rôle neutre
                $user = User::create([
                    'name'                 => $googleUser->name,
                    'email'                => $googleUser->email,
                    'google_id'            => $googleUser->id,
                    'avatar'               => $googleUser->avatar,
                    'password'             => bcrypt(Str::random(24)),
                    'role'                 => 'locataire', // rôle provisoire, sera changé par l'onboarding
                    'is_active'            => true,
                    'onboarding_completed' => false,       // ← DOIT faire l'onboarding
                    'email_verified_at'    => now(),
                    'google_token'         => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);

                // LOG DE CRÉATION
                \App\Models\ActivityLog::log('creation', 'a créé son compte via Google', $user);

                // NOTIFICATION AUX ADMINS
                $admins = User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send(
                    $admins,
                    new \App\Notifications\NewUserRegistered($user)
                );
            }

            // On connecte l'utilisateur
            Auth::login($user);

            // RÈGLE DE REDIRECTION :
            // Les rôles "internes" (admin, gestionnaire, comptable) vont direct au dashboard
            $rolesInternes = ['admin', 'gestionnaire', 'comptable'];
            if (in_array($user->role, $rolesInternes)) {
                return redirect()->intended('dashboard');
            }

            // Pour locataire et propriétaire : vérifier si l'onboarding est fait
            if (!$user->onboarding_completed) {
                return redirect()->route('onboarding.index');
            }

            return redirect()->intended('dashboard');

        } catch (Exception $e) {
            return redirect()->route('login')
                             ->with('error', 'Erreur lors de la connexion avec Google : ' . $e->getMessage());
        }
    }
}
