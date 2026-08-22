<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'time_start',
        'time_end',
        'start_time',
        'end_time',
        'display_name',
        'display_time',
        'sort_order',
    ];
}
