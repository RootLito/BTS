<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTracking extends Model
{
    protected $table = 'document_trackings';

    protected $fillable = [
        'trip_ticket_id',
        'client_id',
        'document_no',
        'route_from',     
        'route_to',
        'status',
        'date_released',
        'date_received',
        'remarks',
    ];

    protected $casts = [
        'date_released' => 'datetime',
        'date_received' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_ticket_id');
    }
}