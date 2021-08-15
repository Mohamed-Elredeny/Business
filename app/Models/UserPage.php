<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserPage extends Model
{
    protected $table ='user_pages';
    protected $fillable=[
        'user_id',
        'page_id',
        'isAdmin'
    ];

}
