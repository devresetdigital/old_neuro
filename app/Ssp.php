<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Ssp extends Model
{
    use SoftDeletes;

    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_ssps', 'ssp_id', 'strategy_id');
    }
    
}
