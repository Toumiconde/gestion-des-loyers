<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = [
        'cle',
        'valeur',
        'description',
        'updated_by',
    ];

    // Qui a modifié ce paramètre en dernier
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Récupérer un paramètre facilement depuis n'importe où
    // Usage : Parametre::get('devise') → 'GNF'
    public static function getValue(string $cle): ?string
    {
        return static::where('cle', $cle)->value('valeur');
    }
}