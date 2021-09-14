<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = [
        'filename',
        'mediaType',
        'model_id',
        'model_type'
    ];
    public function post() {
        return $this->belongsTo('App\Models\Post','model_id');
    }

}
