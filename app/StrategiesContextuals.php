<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesContextuals extends Model
{
    public function Strategy(){
        return $this->belongsTo('App\Strategy');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','contextual_id','inc_exc'];
}
