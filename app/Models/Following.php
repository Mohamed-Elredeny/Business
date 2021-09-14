<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $table = 'following';
    protected $fillable = [
        'followerId',
        'followingId',
    ];
    function follower(){
        return $this->belongsTo(User::class,'followerId');
    }
    function following(){
        return $this->belongsTo(User::class,'followingId');
    }
}
