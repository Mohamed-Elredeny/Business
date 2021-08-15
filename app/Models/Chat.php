<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    protected $fillable = [
        'contacts'
    ];
    public function messages() {
        return $this->hasMany('App\Models\Message');
    }
}
