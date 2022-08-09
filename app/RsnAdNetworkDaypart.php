<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnAdNetworkDaypart extends Model
{
    protected $table = 'rsn_ad_network_daypart';

    public $timestamps = false;

    public function RsnAds(){
        return $this->belongsTo('App\RsnAds', 'ad_id');
    }    
    public function RsnNetworks(){
        return $this->belongsTo('App\RsnNetworks', 'network_id');
    }    
    public function RsnDayparts(){
        return $this->belongsTo('App\RsnDayparts', 'daypart_id');
    }
}
