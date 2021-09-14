<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $table ='sports';
    protected $fillable = [
        'name_en','name_ar'	,'image'
    ];
}
