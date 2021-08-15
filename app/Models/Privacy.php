<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{
    protected $table = 'privacy_type';
    protected $fillable = [
           'name'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
