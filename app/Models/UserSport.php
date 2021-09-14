<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserSport extends Model
{
    protected $table ='user_sports';
    protected $fillable = [
        'userId','sportId'
    ];
    public function user(){
        return $this->belongsTo(User::class,'userId');
    }
    public function music(){
        return $this->belongsTo(Sport::class,'sportId');
    }
}
