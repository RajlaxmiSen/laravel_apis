<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoVote extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'photo_id'
    ];
    
    protected $dates = ['deleted_at'];
}
