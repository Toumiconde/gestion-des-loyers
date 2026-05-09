<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenancier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'specialite',
        'telephone',
        'email',
        'disponibilite',
        'notes',
    ];

    // Un maintenancier peut être assigné à plusieurs incidents
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
