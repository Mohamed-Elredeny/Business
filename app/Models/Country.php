<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $tableName = 'countries';
    protected $fillable=[
        'name'
    ];
}
