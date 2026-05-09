<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietaire extends Model
{
    use SoftDeletes;
    // Colonnes qu'on autorise à remplir en masse
    protected $fillable = [
        'user_id',
        'telephone',
        'adresse',
        'adresse_professionnelle',
        'rib_bancaire',
        'nom_banque',
        'titulaire_compte',
        'signature',
    ];

    // Un propriétaire EST un user
    // proprietaires.user_id → users.id
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un propriétaire a plusieurs biens
    // biens.proprietaire_id → proprietaires.id
    public function biens()
    {
        return $this->hasMany(Bien::class);
    }
}