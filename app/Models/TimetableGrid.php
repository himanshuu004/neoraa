<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableGrid extends Model
{
    protected $table = 'timetable_grid';

    public $timestamps = false;

    public function kid(): BelongsTo
    {
        return $this->belongsTo(Kid::class, 'kid_id', 'kid_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }
}
