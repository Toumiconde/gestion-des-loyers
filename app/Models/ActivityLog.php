<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Pas de updated_at pour les logs, on ne modifie jamais un log
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array', // JSON automatiquement converti en tableau PHP
    ];

    // Quel user a fait cette action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}