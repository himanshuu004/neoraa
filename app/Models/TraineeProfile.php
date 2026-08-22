<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeProfile extends Model
{
    protected $table = 'trainee_profile';

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
