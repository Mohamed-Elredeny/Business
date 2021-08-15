<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class source extends Model
{
    protected $table = 'sources';
    protected $fillable = [
        'sourceTypeId',
        'sourceId'
    ];

    public function sourceType()
    {
        return $this->belongsTo(sourceTypes::class);
    }
}
