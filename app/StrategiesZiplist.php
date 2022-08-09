<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesZiplist extends Model
{
    public function Ziplist(){
        return $this->belongsTo('App\Ziplist');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','ziplist_id','inc_exc'];
}
