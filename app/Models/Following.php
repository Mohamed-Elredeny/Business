<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $table = 'following';
    protected $fillable = [
        'followerId',
        'followingId',
    ];

}
