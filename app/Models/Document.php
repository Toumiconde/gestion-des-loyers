<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nom',
        'type',
        'chemin',
        'taille_ko',
        'viewed_at',
        'uploaded_by',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Relation polymorphique — ce document peut appartenir
    // à un Bien, un Contrat, un Locataire...
    public function documentable()
    {
        return $this->morphTo();
    }

    // Qui a uploadé ce document
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}