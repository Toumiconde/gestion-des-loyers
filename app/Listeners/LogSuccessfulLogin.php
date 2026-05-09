<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $role = $user->role ?? 'utilisateur';
        
        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'connexion',
            'details'     => ['message' => "Connexion réussie (Rôle: {$role})"],
            'ip_address'  => request()->ip(),
        ]);
    }
}
