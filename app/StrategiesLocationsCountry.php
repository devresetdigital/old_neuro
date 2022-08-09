<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesLocationsCountry extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','country','inc_exc'];
}
