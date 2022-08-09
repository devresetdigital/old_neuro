<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesSsp extends Model
{
    public function Ssp(){
        return $this->belongsTo('App\Ssp');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','ssp_id','inc_exc'];
}
