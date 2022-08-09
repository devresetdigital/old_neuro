<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pixel extends Model
{
    use SoftDeletes;

    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
    
}
