<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'contrat_id',
        'declare_par',
        'titre',
        'description',
        'photo_incident',
        'priorite',
        'cout_estime',
        'cout_reel',
        'technicien_nom',
        'technicien_tel',
        'statut',
        'is_new',
        'date_resolution',
    ];

    protected $casts = [
        'date_resolution' => 'date',
        'is_new'          => 'boolean',
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