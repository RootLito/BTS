<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripTicket extends Model
{
    protected $fillable = [
        'purpose',
        'destination',
        'start_date',
        'end_date',
        'passengers',
        'driver_id',
        'vehicle_id',
        'status',
    ];

    protected $casts = [
        'passengers' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function driver(): BelongsTo {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class);
    }
}