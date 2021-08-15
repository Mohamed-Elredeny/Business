<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PageMember extends Model
{
    protected $table="page_members";
    protected $fillable = [
        'user_id',
        'page_id',
        'state',
        'isAdmin'
    ];
    
    public function member()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function page()
    {
        return $this->belongsTo('App\Models\Page', 'page_id');
    }
}
