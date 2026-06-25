<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToReport extends Model
{
    protected $fillable = [
        'trip_ticket_id',
        'national_to_id',
        'outputs',
    ];


    public function tripTicket(): BelongsTo
    {
        return $this->belongsTo(TripTicket::class, 'trip_ticket_id');
    }

    public function nationalTo(): BelongsTo
    {
        return $this->belongsTo(NationalTo::class, 'national_to_id');
    }
}