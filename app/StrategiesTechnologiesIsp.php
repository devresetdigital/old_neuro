<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesTechnologiesIsp extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','isp_id','inc_exc'];
}
