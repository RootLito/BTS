<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NationalTo extends Model
{
    use HasFactory;

    protected $table = 'national_to';

    protected $fillable = [
        'client_id', 
        'to_no',
        'date',
        'personnel',
        'route',
        'departure',
        'return_date',
        'purpose',
        'rd',
        'oic',
    ];

    protected $casts = [
        'date' => 'date',
        'departure' => 'date',
        'return_date' => 'date',
        'personnel' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}