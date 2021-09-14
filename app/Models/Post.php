<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //'
    protected $table = 'posts';
    protected $fillable = [
        'id',
        'price',
        'body',
        'tags',
        'postTypeId',
        'privacyId',
        'stateId',
        'publisherId',
        'categoryId',
        'group_id',
        'post_id',
        'page_id',
        'mentions',
        'country'
    ];

    public function media() {
        return $this->hasMany('App\Models\Media','id');
    }

    public function state() {
        return $this->belongsTo('App\Models\State','stateId');
    }

    public function privacy() {
        return $this->belongsTo('App\Models\Privacytype','privacyId');
    }

    public function type() {
        return $this->belongsTo('App\Models\Posttypes','postTypeId');
    }

    public function publisher()
    {
        return $this->belongsTo('App\User', 'publisherId');

    }

}
