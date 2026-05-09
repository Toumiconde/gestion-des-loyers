<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\ProfileUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Gestion de la photo de profil
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        // LOGIQUE SPÉCIFIQUE PROPRIÉTAIRE
        if ($user->role === 'proprietaire' && $user->proprietaire) {
            $proprietaire = $user->proprietaire;
            
            $pData = $request->only(['telephone', 'adresse', 'rib_bancaire', 'adresse_professionnelle']);
            
            // Signature (Base64 ou Fichier)
            if ($request->filled('signature_data')) {
                if ($proprietaire->signature) Storage::disk('public')->delete($proprietaire->signature);
                $data = $request->input('signature_data');
                $image = str_replace('data:image/png;base64,', '', $data);
                $image = str_replace(' ', '+', $image);
                $imageName = 'signatures/sig_' . time() . '_' . $user->id . '.png';
                Storage::disk('public')->put($imageName, base64_decode($image));
                $pData['signature'] = $imageName;
            } elseif ($request->hasFile('signature')) {
                if ($proprietaire->signature) Storage::disk('public')->delete($proprietaire->signature);
                $pData['signature'] = $request->file('signature')->store('signatures', 'public');
            }

            $proprietaire->update($pData);
        }

        // LOGIQUE SPÉCIFIQUE LOCATAIRE
        if ($user->role === 'locataire' && $user->locataire) {
            $locataire = $user->locataire;
            $lData = $request->only(['telephone', 'adresse']);
            
            // Mise à jour de l'email aussi dans le modèle locataire pour cohérence
            if ($user->isDirty('email')) {
                $lData['email'] = $user->email;
            }

            $locataire->update($lData);
        }

        // Enregistrer une notification pour l'admin si l'utilisateur n'est pas admin
        if ($user->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();
            $msg = "Le {$user->role} {$user->name} a mis à jour son profil.";
            
            Notification::send($admins, new ProfileUpdated($user, $msg));

            // Si c'est un locataire, on prévient aussi son propriétaire
            if ($user->role === 'locataire' && $user->locataire) {
                $contrat = $user->locataire->contratActif;
                if ($contrat && $contrat->bien && $contrat->bien->proprietaire) {
                    $ownerUser = $contrat->bien->proprietaire->user;
                    if ($ownerUser) {
                        $ownerUser->notify(new ProfileUpdated($user, "Votre locataire {$user->name} a mis à jour ses informations."));
                    }
                }
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'profile_updated',
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
                'details' => [
                    'message' => $msg,
                    'changes' => $user->getChanges(),
                ],
                'ip_address' => $request->ip(),
            ]);
        }

        // Déconnexion automatique pour vérification (comme demandé)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login')->with('success', 'Vos informations ont bien été changées. Veuillez vous reconnecter.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // SÉCURITÉ : Vérification des dettes pour les locataires
        if ($user->role === 'locataire' && $user->locataire) {
            $contrat = $user->locataire->contratActif;
            if ($contrat) {
                $dettesCount = $contrat->paiements()->where('statut', '!=', 'paye')->count();
                if ($dettesCount > 0) {
                    return back()->with('error', "🚨 Impossible de supprimer votre compte : vous possédez encore {$dettesCount} paiement(s) non régularisé(s). Veuillez contacter l'administration.");
                }
            }
        }

        // PRÉPARATION DES NOTIFICATIONS (Avant suppression)
        $admins = User::where('role', 'admin')->get();
        $msg = "⚠️ COMPTE SUPPRIMÉ : Le {$user->role} {$user->name} a supprimé son compte définitivement.";
        
        // Notifier l'admin
        Notification::send($admins, new ProfileUpdated($user, $msg));

        // Si c'est un locataire, on prévient son propriétaire
        if ($user->role === 'locataire' && $user->locataire) {
            $contrat = $user->locataire->contratActif;
            if ($contrat && $contrat->bien && $contrat->bien->proprietaire) {
                $ownerUser = $contrat->bien->proprietaire->user;
                if ($ownerUser) {
                    $ownerUser->notify(new ProfileUpdated($user, "⚠️ DÉPART : Votre locataire {$user->name} a supprimé son compte. Veuillez vérifier l'état du logement."));
                }
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Votre compte a été supprimé avec succès.');
    }
}
