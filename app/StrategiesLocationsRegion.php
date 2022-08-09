<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesLocationsRegion extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','region','inc_exc'];
}
