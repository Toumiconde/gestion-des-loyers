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
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Helper pour logger une activité
     */
    public static function log($action, $details = null, $model = null)
    {
        return self::create([
            'user_id'     => auth()->id() ?? 1, // Fallback admin si non auth (rare)
            'action'      => $action,
            'details'     => is_string($details) ? ['message' => $details] : $details,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model ? $model->id : null,
            'ip_address'  => request()->ip(),
        ]);
    }
}