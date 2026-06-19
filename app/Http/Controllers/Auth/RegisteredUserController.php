<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:proprietaire,locataire'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'locataire',
            'is_active' => true,
        ]);

        // CRÉATION AUTOMATIQUE DU PROFIL SELON LE RÔLE
        if ($user->role === 'proprietaire') {
            \App\Models\Proprietaire::create([
                'user_id' => $user->id,
                // Le propriétaire n'a pas de champ nom (on utilise celui du User)
            ]);
        } elseif ($user->role === 'locataire') {
            // Logique de séparation nom/prénom plus robuste
            $parts = explode(' ', trim($user->name));
            if (count($parts) > 1) {
                $prenom = $parts[0];
                $nom = implode(' ', array_slice($parts, 1));
            } else {
                $prenom = ''; // Prénom optionnel si un seul mot
                $nom = $parts[0];
            }

            \App\Models\Locataire::create([
                'user_id' => $user->id,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $user->email,
            ]);
        }

        event(new Registered($user));

        // NOTIFICATION AUX ADMINS ET GESTIONNAIRES
        $staff = User::whereIn('role', ['admin', 'gestionnaire'])->get();
        foreach ($staff as $member) {
            $member->notify(new \App\Notifications\NewUserRegisteredNotification($user));
        }

        // LOG D'ACTIVITÉ
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'inscription',
            'description' => "Nouvelle inscription d'un " . $user->role . " : " . $user->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Auth::login($user);

        $msg = $user->role === 'proprietaire' ? 'Bienvenue Propriétaire ! Commencez à gérer vos biens.' : 'Bienvenue Locataire ! Accédez à vos documents.';

        return redirect(route('dashboard'))->with('success', $msg);
    }
}
