<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesCustomData extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','custom_datas','inc_exc'];
}
