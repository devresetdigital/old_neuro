<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesSegment extends Model
{
    public function Strategy(){
        return $this->belongsTo('App\Strategy');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','segment_id','inc_exc','segment_targets','data_cpm'];
}
