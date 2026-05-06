<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relance extends Model
{
    protected $fillable = [
        'contrat_id',
        'niveau',
        'canal',
        'statut',
        'date_envoi',
        'acquitte_par',
        'date_acquittement',
    ];

    protected $casts = [
        'date_envoi'        => 'datetime',
        'date_acquittement' => 'datetime',
    ];

    // Une relance appartient à un contrat
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    // Qui a acquitté la relance
    public function acquittePar()
    {
        return $this->belongsTo(User::class, 'acquitte_par');
    }
}