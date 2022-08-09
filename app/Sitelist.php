<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Sitelist extends Model
{
    use SoftDeletes;

    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_sitelists', 'sitelist_id', 'strategy_id');
    }
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
}
