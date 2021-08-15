<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class userInterests extends Model
{
    protected $table = 'user_interests';
    protected $fillable = [
        'userId',
        'interestId'
    ];

}
