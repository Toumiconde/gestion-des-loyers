<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locataire extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'piece_identite',
    ];

    // Un locataire peut avoir un compte user (optionnel)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un locataire a plusieurs contrats
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    // Le contrat actif du locataire
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    // Nom complet en un seul appel : $locataire->nom_complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
}