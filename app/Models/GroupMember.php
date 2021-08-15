<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table="group_members";
    protected $fillable = [
      'user_id',
      'group_id',
      'state',
       'isAdmin'
    ];
    public function member()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id');
    }
}
