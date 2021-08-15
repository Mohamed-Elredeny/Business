<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
  protected $table="pages";
  protected $fillable = [
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
