<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $table = 'friendships';
    protected $fillable = [
        'senderId',
        'receiverId',
        'stateId'
    ];

    public function userSender()
    {
        return $this->belongsTo('App\User', 'senderId');
    }

    public function userResever()
    {
        return $this->belongsTo('App\User', 'receiverId');
    }

    // public function groupmember()
    // {
    //     return $this->hasOne('App\Models\GroupMember', 'user_id' , 'receiverId');
    // }
}
