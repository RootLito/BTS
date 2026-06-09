<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToReport extends Model
{
    protected $fillable = [
        'trip_ticket_id',
        'outputs',
    ];

    /**
     * Get the trip ticket that owns this report.
     */
    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class);
    }
}
