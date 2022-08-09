<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pmp extends Model
{

    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_pmps', 'pmp_id', 'strategy_id');
    }
    public function Ssp(){
        return $this->belongsTo('App\Ssp');
    }
}
