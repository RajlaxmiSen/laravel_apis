<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedValueAdded extends Model
{
    use SoftDeletes;

   	protected $dates = ['deleted_at'];
    protected $fillable = ['feed_id', 'user_id'];

    public function feed() {
        return $this->belongsTo('App\Models\Feed', 'feed_id');
    }
}
