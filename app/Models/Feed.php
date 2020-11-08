<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'feed_text', 'likes_count' ,'share_count', 'value_added', 'status', 'admin_comment','is_share','shared_feed_id'];

    public function images()
    {
        return $this->hasOne('App\Models\FeedImage','feed_id');
    }

    public function video()
    {
        return $this->hasOne('App\Models\FeedVideo','feed_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\FeedComment','feed_id');
    }

    public function like()
    {
        return $this->hasOne('App\Models\FeedLike','feed_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function userProfile() {
        return $this->belongsTo('App\Models\UserProfile','user_id');
    }

    public function valueAdd()
    {
        return $this->hasOne('App\Models\FeedValueAdded','feed_id');
    }
}
