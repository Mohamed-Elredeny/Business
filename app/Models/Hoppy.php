<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hoppy extends Model
{
    protected $table ='hoppies';
    protected $fillable = [
        'name_en','name_ar'	,'image'
    ];
}
