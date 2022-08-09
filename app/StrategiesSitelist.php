<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesSitelist extends Model
{
    public function Sitelist(){
        return $this->belongsTo('App\Sitelist');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','sitelist_id','inc_exc'];
}
