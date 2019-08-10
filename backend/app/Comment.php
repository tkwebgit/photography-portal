<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];
    public function upload(){
        return $this->belongsTo('App\Upload');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
