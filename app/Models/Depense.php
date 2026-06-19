<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_depense' => 'date',
        ];
    }
}
