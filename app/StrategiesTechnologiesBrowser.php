<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesTechnologiesBrowser extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','browser_id'];
}
