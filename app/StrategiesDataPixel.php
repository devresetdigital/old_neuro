<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesDataPixel extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','pixels','inc_exc'];
}
