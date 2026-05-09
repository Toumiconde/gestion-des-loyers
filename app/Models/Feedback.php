<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'stars',
        'comment',
        'status',
        'admin_note',
        'is_announcement',
        'parent_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(Feedback::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Feedback::class, 'parent_id');
    }
}
