<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reversement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'proprietaire_id',
        'periode',
        'montant_total_loyers',
        'commission_agence',
        'montant_net',
        'date_reversement',
        'mode_paiement',
        'reference_transaction',
        'statut',
        'preuve_paiement',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_reversement' => 'date',
    ];

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
