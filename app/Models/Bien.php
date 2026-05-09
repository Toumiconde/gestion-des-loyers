<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'proprietaire_id',
        'libelle',
        'type',
        'adresse',
        'surface',
        'loyer_base',
        'charges',
        'depot_garantie',
        'statut',
        'nombre_chambres',
        'type_douche',
    ];

    // Un bien appartient à un propriétaire
    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    // Un bien a plusieurs contrats (historique)
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    // Le contrat actif en cours sur ce bien
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    // Un bien a plusieurs documents (photos, PDF...)
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Un bien (maison) peut avoir plusieurs unités locatives (dalles/appartements)
    public function unitesLocatives()
    {
        return $this->hasMany(UniteLocative::class);
    }

    /**
     * Get the first image document as the main photo
     */
    public function getMainPhotoAttribute()
    {
        $photo = $this->documents()->where('type', 'photo')->first();
        return $photo ? asset('storage/' . $photo->chemin) : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80';
    }
}