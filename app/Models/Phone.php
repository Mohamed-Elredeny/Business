<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = "packaging_companies_phones";

    protected $fillable = [
		"packaging_company_id" ,'phoneNumber'
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', "packaging_company_id");
    }
}
