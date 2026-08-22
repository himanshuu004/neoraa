<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kid extends Model
{
    protected $primaryKey = 'kid_id';

    public $timestamps = false;

    protected $fillable = [
        'kid_name',
        'age',
        'parent_name',
        'contact',
        'case_type',
    ];
}
