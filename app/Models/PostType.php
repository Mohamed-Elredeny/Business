<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    protected $table = 'posts_types';
    protected $fillable = [
           'name'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

}
