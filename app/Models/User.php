<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',       // admin, proprietaire, gestionnaire, locataire, comptable
        'is_active',  // compte actif ou désactivé
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ============================================================
    // HELPERS DE RÔLES
    // Usage : if ($user->isAdmin()) { ... }
    // ============================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProprietaire(): bool
    {
        return $this->role === 'proprietaire';
    }

    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isLocataire(): bool
    {
        return $this->role === 'locataire';
    }

    public function isComptable(): bool
    {
        return $this->role === 'comptable';
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    // Un user peut avoir un profil propriétaire
    public function proprietaire()
    {
        return $this->hasOne(Proprietaire::class);
    }

    // Un user peut avoir un profil locataire
    public function locataire()
    {
        return $this->hasOne(Locataire::class);
    }

    // Toutes les actions de ce user dans les logs
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}