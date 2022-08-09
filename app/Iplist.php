<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iplist extends Model
{
    use SoftDeletes;

    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_iplists', 'iplist_id', 'strategy_id');
    }
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
}
