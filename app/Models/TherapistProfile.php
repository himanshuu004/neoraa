<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistProfile extends Model
{
    protected $table = 'therapist_profile';

    protected $fillable = [
        'user_id',
        'name',
        'contact',
        'email',
        'profile_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
