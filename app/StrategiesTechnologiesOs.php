<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesTechnologiesOs extends Model
{
    protected $table = 'strategies_technologies_oss';
    public $timestamps = false;
    protected $fillable = ['strategy_id','os','inc_exc'];
}
