<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Concept extends Model
{
    use SoftDeletes;
    public function Creatives(){
        return $this->hasMany('App\Creative');
    }

    public function Strategies(){
        return $this->belongsToMany('App\Strategy', 'strategies_concepts');
    }
    public function Advertiser()
    {
        return $this->belongsTo('App\Advertiser');
    }
}
