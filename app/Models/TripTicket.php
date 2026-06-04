<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripTicket extends Model
{
    protected $fillable = [
        'to_no',
        'client_id',
        'office',
        'purpose',
        'destination',
        'start_date',
        'end_date',
        'passengers',
        'driver_id',
        'vehicle_id',
        'status',
        'supervisor',
        'passengers2',
        'purpose2',
    ];

    protected $casts = [
        'passengers' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'trip_id');
    }

    public function documentTrackings(): HasMany
    {
        return $this->hasMany(DocumentTracking::class, 'trip_ticket_id');
    }
}
