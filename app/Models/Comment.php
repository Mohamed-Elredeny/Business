<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    //
    use Notifiable;

    protected $fillable = [
        'body','model_id','comment_id','belong_to','user_id','model_type','mentions'
    ];
}
