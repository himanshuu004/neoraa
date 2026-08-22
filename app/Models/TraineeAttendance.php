<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TraineeAttendance extends Model
{
    protected $table = 'trainee_attendance';

    protected $fillable = [
        'trainee_id',
        'session_date',
        'child_name',
        'activity_description',
        'session_image',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TraineeSessionImage::class, 'attendance_id');
    }
}
