<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilan extends Model
{
    protected $fillable = [
        'proprietaire_id',
        'mois',
        'annee',
        'total_revenus',
        'total_depenses',
        'montant_net',
        'envoye_le',
        'consulte_le',
        'pdf_path',
    ];

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }
}
