<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'client_id',
        'trip_id',
        'note',
        'is_read',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_id');
    }
}