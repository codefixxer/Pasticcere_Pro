<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'is_active'  ,  'image'   ];

    // Relationship to Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    protected $casts = [
        'event_date' => 'date',    // now $news->event_date is a Carbon instance
    ];
};