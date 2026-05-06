<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'numero_contrat',
        'bien_id',
        'locataire_id',
        'date_debut',
        'date_fin',
        'duree_mois',
        'loyer',
        'depot_garantie',
        'jour_echeance',
        'statut',
        'motif_resiliation',
        'date_resiliation',
        'taux_revision',
    ];

    // Les dates sont automatiquement converties en objets Carbon
    protected $casts = [
        'date_debut'      => 'date',
        'date_fin'        => 'date',
        'date_resiliation'=> 'date',
    ];

    // Un contrat concerne un bien
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    // Un contrat concerne un locataire
    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    // Un contrat a plusieurs paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Un contrat a plusieurs incidents
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    // Un contrat a plusieurs relances
    public function relances()
    {
        return $this->hasMany(Relance::class);
    }
}