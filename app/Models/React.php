<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class React extends Model
{
    protected $table = 'reacts';
    protected $fillable = [
        'name',
        'image',
    ];
    public function post() {
        return $this->belongsTo('App\Models\Post');
    }

}
