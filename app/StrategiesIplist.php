<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class StrategiesIplist extends Model
{
    public function Iplist(){
        return $this->belongsTo('App\Iplist');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','iplist_id','inc_exc'];
}
