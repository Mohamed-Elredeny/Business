<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badword extends Model
{

    protected $table = "bad_words";

    protected $fillable = [
        'name'
    ];
}
