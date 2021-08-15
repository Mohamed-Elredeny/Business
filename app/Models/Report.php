<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $fillable = [
           'model_id',
           'body',
           'state',
            'model_type',
        'user_id'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
