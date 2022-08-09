<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesPmp extends Model
{
    public function Pmp(){
        return $this->belongsTo('App\Pmp');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','pmp_id','inc_exc','open_market'];
}
