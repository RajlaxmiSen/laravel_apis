<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NeedHelpWith extends Model
{
    protected $fillable = ['user_id','category_id'];

    public function user() {
        
        return $this->belongsTo('App\Models\User');
    }
}
