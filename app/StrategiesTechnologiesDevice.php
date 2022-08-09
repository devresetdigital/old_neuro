<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesTechnologiesDevice extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','device_id'];
}
