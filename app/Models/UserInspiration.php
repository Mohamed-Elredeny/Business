<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserInspiration extends Model
{
    protected $table = 'user_inspirations';
    protected $fillable = [
        'inspirerende_id',
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function music(){
        return $this->belongsTo(User::class,'inspirerende_id');
    }
    public $timestamps = false;

}
