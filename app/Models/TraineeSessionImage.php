<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeSessionImage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attendance_id',
        'image_path',
    ];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(TraineeAttendance::class, 'attendance_id');
    }
}
