<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesGeofencing extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','geolocation','inc_exc'];
}
