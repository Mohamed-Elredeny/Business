<?php

namespace App\Models;

use App\User;

use Illuminate\Database\Eloquent\Model;

class UserMusic extends Model
{
    protected $table = 'user_musics';
    protected $fillable = [
        'userId',
        'musicId'
    ];
    public function user(){
        return $this->belongsTo(User::class,'userId');
    }
    public function music(){
        return $this->belongsTo(Music::class,'musicId');
    }
}
