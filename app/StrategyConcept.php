<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StrategyConcept extends Model
{
    protected $table = 'strategies_concepts';

    public function Strategy(){
        return $this->hasOne('App\Strategy');
    }
    public function Concept(){
        return $this->belongsTo('App\Concept');
    }
    public $timestamps = false;
    protected $fillable = ['strategy_id','concept_id'];
}
