<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTracking extends Model
{
    // Explicitly defining the table name
    protected $table = 'document_trackings';

    protected $fillable = [
        'trip_ticket_id',
        'route',
        'status',
        'date_released',
        'date_received',
        'remarks',
    ];

    protected $casts = [
        'date_released' => 'datetime',
        'date_received' => 'datetime',
    ];

    /**
     * Get the trip ticket associated with this tracking entry.
     */
    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_ticket_id');
    }
}