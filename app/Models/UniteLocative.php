<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniteLocative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unites_locatives';

    protected $fillable = [
        'bien_id',
        'libelle',
        'niveau',
        'nombre_chambres',
        'surface',
        'prix_loyer',
        'statut',
        'description',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function demandes()
    {
        return $this->hasMany(DemandeLocation::class);
    }

    public function scopeLibre($query)
    {
        return $query->where('statut', 'libre');
    }
}
