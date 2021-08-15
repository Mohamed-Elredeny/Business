<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = 'packaging_companies';
    protected $fillable = [
        'name','details','image', "stateId",'country'
    ];

    public function phone() {
        return $this->hasMany('App\Models\Phone', "packaging_company_id");
    }

}
