<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keywordslist extends Model
{
    use SoftDeletes;

    public function Strategies(){
        return $this->belongsToMany('App\Strategy','strategies_keywordslist', 'keywordslist_id', 'strategy_id');
    }
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
    
}
