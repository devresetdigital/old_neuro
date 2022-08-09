<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesPublisherlist extends Model
{
    public function Publisherlist(){
        return $this->belongsTo('App\Publisherlist');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','publisherlist_id','inc_exc'];
}
