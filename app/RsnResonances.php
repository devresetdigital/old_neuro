<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnResonances extends Model
{
    protected $table = 'rsn_resonances';
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
    public function RsnPrograms(){
        return $this->belongsTo('App\RsnPrograms', 'program_id');
    }
    public function RsnProgramGenres(){
        return $this->belongsTo('App\RsnProgramGenres', 'program_genre_id');
    }
}
