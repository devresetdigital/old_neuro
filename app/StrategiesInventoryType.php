<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesInventoryType extends Model
{
    public $timestamps = false;
    protected $fillable = ['strategy_id','inventory_type','inc_exc'];
}
