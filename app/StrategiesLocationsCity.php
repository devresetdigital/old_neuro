<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesLocationsCity extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','city','inc_exc'];
}
