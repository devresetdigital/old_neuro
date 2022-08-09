<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesIsp extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','isps','inc_exc'];
}
