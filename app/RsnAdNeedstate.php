<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnAdNeedstate extends Model
{
    protected $table = 'rsn_ad_needstates';
    public $timestamps = false;

    public function RsnNeedstates(){
        return $this->belongsTo('App\RsnNeedstates' , 'needstate_id');
    }

    public function RsnAds(){
        return $this->belongsTo('App\RsnAds' , 'ad_id');
    }


}
