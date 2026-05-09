<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetByAdminMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminActionController extends Controller
{
    /**
     * Réinitialise le mot de passe d'un utilisateur, puis lui envoie
     * le nouveau mot de passe temporaire par email.
     */
    public function resetPassword(Request $request, User $user)
    {
        // Sécurité : Seul l'admin peut réinitialiser le mot de passe.
        if (!auth()->user()->isAdmin()) {
            abort(403, "Action réservée à l'administrateur.");
        }

        // Générer un mot de passe temporaire lisible et sécurisé
        $newPassword = 'Gest' . Str::random(6) . '!';

        // Mise à jour du mot de passe en base
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Envoi de l'email si l'utilisateur a une adresse email
        $emailSent = false;
        if ($user->email) {
            try {
                Mail::to($user->email)->send(
                    new PasswordResetByAdminMail($user, $newPassword)
                );
                $emailSent = true;
            } catch (\Exception $e) {
                // En cas d'échec SMTP, on continue sans bloquer l'admin.
                // Le mot de passe est quand même réinitialisé.
                \Log::warning("Échec envoi email reset pour {$user->email}: " . $e->getMessage());
            }
        }

        // Log de l'action dans le journal d'activité
        \App\Models\ActivityLog::log(
            'securite',
            "Réinitialisation du mot de passe pour {$user->name} ({$user->role})" .
            ($emailSent ? ' — Email de confirmation envoyé.' : ' — Email non disponible.'),
            $user
        );

        return back()->with('password_reset_success', [
            'name'          => $user->name,
            'email'         => $user->email,
            'temp_password' => $newPassword,
            'email_sent'    => $emailSent,
        ]);
    }
}
