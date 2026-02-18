<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'date',
        'personnel',      
        'departure',
        'return',
        'destination',
        'purpose',
        'recommended_by',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'personnel' => 'array',      
        'recommended_by' => 'array', 
    ];

    /**
     * Get the Trip Ticket associated with the Travel Order.
     */
    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_id');
    }
}
