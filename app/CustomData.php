<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CustomData extends Model
{
    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_custom_datas', 'custom_datas', 'strategy_id');
    }
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
}
