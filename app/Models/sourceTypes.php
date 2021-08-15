<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sourceTypes extends Model
{
    protected $table = 'sources_types';
    protected $fillable = [
           'name'
    ];
}
