<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'trip_id',
        'message',
        'is_viewed',
        'is_admin'
    ];


    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_id');
    }

    
}