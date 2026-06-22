<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'username',
        'password',
        'office',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
    public function documentTrackings(): HasMany
    {
        return $this->hasMany(DocumentTracking::class);
    }
    public function nationalTos(): HasMany
    {
        return $this->hasMany(NationalTo::class, 'client_id');
    }
}