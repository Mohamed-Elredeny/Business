<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserHoppy extends Model
{
    protected $table ='user_hoppies';
    protected $fillable = [
        'userId','hoppieId'
    ];
    public function user(){
        return $this->belongsTo(User::class,'userId');
    }
    public function music(){
        return $this->belongsTo(Hoppy::class,'hoppieId');
    }
}
