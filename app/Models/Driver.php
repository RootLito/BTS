<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact', 'status', 'vehicle_id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
