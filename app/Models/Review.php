<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'text',
        'author',
        'location',
        'photo_path',
        'display_order',
    ];
}
