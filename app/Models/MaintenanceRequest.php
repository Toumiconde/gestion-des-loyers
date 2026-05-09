<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'auto_response',
        'admin_response',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
