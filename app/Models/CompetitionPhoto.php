<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionPhoto extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'comp_id', 'photo_path', 'vote_count', 'is_winner', 'state', 'state_id', 'country', 'country_id'
    ];
    
    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function userProfile() {
        return $this->belongsTo('App\Models\UserProfile','user_id');
    }
}
