<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapySession extends Model
{
    protected $table = 'sessions';

    public $timestamps = false;

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'time_slot',
        'kid_id',
        'client_name',
        'session_type',
        'special_notes',
    ];

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function kid(): BelongsTo
    {
        return $this->belongsTo(Kid::class, 'kid_id', 'kid_id');
    }
}
