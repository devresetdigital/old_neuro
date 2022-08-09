<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Advertiser extends Model
{
    use SoftDeletes;
    
    public function organization() 
    {
        return $this->belongsTo('App\Organization');
    } 
}
