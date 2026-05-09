<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandeLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demandes_location';

    protected $fillable = [
        'unite_locative_id',
        'user_id',
        'statut',
        'message',
        'is_new',
    ];

    public function uniteLocative()
    {
        return $this->belongsTo(UniteLocative::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending()
    {
        return $this->statut === 'en_attente';
    }

    public function isValidatedByOwner()
    {
        return in_array($this->statut, ['valide_proprietaire', 'valide_admin', 'accepte']);
    }

    public function isValidatedByAdmin()
    {
        return in_array($this->statut, ['valide_admin', 'accepte']);
    }
}
