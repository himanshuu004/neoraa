<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionBooking extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'phone',
        'service',
        'message',
        'status',
    ];
}
