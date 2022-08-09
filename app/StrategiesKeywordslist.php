<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategiesKeywordslist extends Model
{
    public function Keywordslist(){
        return $this->belongsTo('App\Keywordslist');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','keywordslist_id','inc_exc'];
}
