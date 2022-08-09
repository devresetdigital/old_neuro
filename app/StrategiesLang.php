<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesLang extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','lang','inc_exc'];
}
