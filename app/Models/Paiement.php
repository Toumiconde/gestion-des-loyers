<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'contrat_id',
        'mois_concerne',
        'montant',
        'date_paiement',
        'mode_reglement',
        'reference',
        'statut',
        'penalite',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'mois_concerne' => 'date',
        'date_paiement' => 'date',
    ];

    // Un paiement appartient à un contrat
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    // Un paiement a une quittance (si statut = payé)
    public function quittance()
    {
        return $this->hasOne(Quittance::class);
    }

    // Qui a enregistré ce paiement
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}