<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'contrat_id',
        'declare_par',
        'titre',
        'description',
        'priorite',
        'statut',
        'date_resolution',
    ];

    protected $casts = [
        'date_resolution' => 'date',
    ];

    // Un incident appartient à un contrat
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    // Qui a déclaré l'incident
    public function declarePar()
    {
        return $this->belongsTo(User::class, 'declare_par');
    }
}