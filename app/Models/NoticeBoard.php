<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeBoard extends Model
{
    protected $table = 'notice_board';

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'priority',
        'status',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
