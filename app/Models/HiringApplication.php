<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiringApplication extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'city',
        'applying_for',
        'qualification',
        'college',
        'year',
        'experience_type',
        'experience_years',
        'current_place',
        'areas_specialization',
        'languages',
        'joining_time',
        'resume_path',
        'certificate_path',
        'why_join_neora',
        'status',
        'note',
        'generated_by',
    ];

    public function generatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
