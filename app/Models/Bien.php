<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    protected $fillable = [
        'proprietaire_id',
        'libelle',
        'type',
        'adresse',
        'surface',
        'loyer_base',
        'charges',
        'depot_garantie',
        'statut',
    ];

    // Un bien appartient à un propriétaire
    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    // Un bien a plusieurs contrats (historique)
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    // Le contrat actif en cours sur ce bien
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    // Un bien a plusieurs documents (photos, PDF...)
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}