<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table ='groups';
    protected $fillable=[
        'name',
        'profile_image',
        'cover_image',
        'description',
        'category_id',
        'privacy',
        'publisher_id',
        'rules'
    ];

}
