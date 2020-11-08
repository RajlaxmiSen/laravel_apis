<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportedIssue extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'photo_id', 'feed_id'
    ];
    
    protected $dates = ['deleted_at'];
}
