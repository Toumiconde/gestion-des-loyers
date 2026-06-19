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
        'frais_gestion',
        'montant_net',
        'statut',
        'date_virement',
        'ref_virement',
        'envoye_le',
        'consulte_le',
        'pdf_path',
        'mode_paiement',
    ];

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }
}
