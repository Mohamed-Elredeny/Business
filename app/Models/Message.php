<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message','userId','chatId'
    ];

    public function chat() {
        return $this->hasOne('App\Models\Chat');
    }
}
