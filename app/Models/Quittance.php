<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quittance extends Model
{
    protected $fillable = [
        'paiement_id',
        'numero_quittance',
        'pdf_path',
        'envoye_par_email',
        'date_envoi',
    ];

    protected $casts = [
        'envoye_par_email' => 'boolean',
        'date_envoi'       => 'datetime',
    ];

    // Une quittance appartient à un paiement
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }
}