<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspiration extends Model
{
    protected $table ='user_inspirations';
    protected $fillable = [
      'user_id',
      'inspirerende_id'
    ];
    public $timestamps = false;


}
