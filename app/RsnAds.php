<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnAds extends Model
{
    protected $table = 'rsn_ads';
    public $timestamps = false;

    public function RsnNetworks(){
        return $this->belongsToMany('App\RsnNetworks', 'rsn_resonances', 'ad_id', 'network_id');
    }

    public function RsnPrograms(){
        return $this->belongsToMany('App\RsnPrograms', 'rsn_resonances', 'ad_id', 'program_id');
    }

    public function RsnProgramGenres(){
        return $this->belongsToMany('App\RsnProgramGenres', 'rsn_resonances', 'ad_id', 'program_genre_id');
    }

    public function RsnNeedstates(){
        return $this->belongsToMany('App\RsnNeedstates', 'rsn_ad_needstates', 'ad_id', 'needstate_id')->withPivot('value');
    }
    

    public function RsnDayparts(){
        return $this->belongsToMany('App\RsnDayparts', 'rsn_ad_network_daypart', 'ad_id', 'daypart_id');
    }

    public function RsnNetworksLift(){
        return $this->belongsToMany('App\RsnNetworks', 'rsn_ad_network_daypart', 'ad_id', 'network_id')->withPivot('resonance_score');
    }
    

    protected $appends = ['DaypartsNames'];


    public function getDaypartsNamesAttribute()
    {   
          
        $data = $this->RsnDayparts->map(function ($aux){
            return [
                "id" => $aux->id,
                "name" => $aux->name,
            ];
        });

        return $data;
    }
    public function getDaypartsCountAttribute()
    {
        return  count($this->RsnDayparts->groupBy('name'));
    }
    public function getNeedstatesCountAttribute()
    {
        return  count($this->RsnNeedstates);
    }
    public function getProgramsCountAttribute()
    {

        return  count($this->RsnPrograms->groupBy('name'));
    }
    public function getProgramGenresCountAttribute()
    {
        return  count($this->RsnProgramGenres->groupBy('name'));
    }
    public function getNetworksCountAttribute()
    {
        return  count($this->RsnNetworks->groupBy('name'));
    }


    private $NCS_LIFT = [
        26,28,29,31,32,34,35,37,38,39,40,44,49,53,58,62,67,71,76,80,81,81,82,82,83,83,84,
        84,85,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,
        107,108,109,110,111,112,113,114,115,116,116,117,117,118,118,119,119,120,120,121,121,
        122,122,123,123,124,124,125,125,129,133,137,141,145,149,153,157,161,165,169,173,177,
        181,185,189,193,197,201,205,209
    ];


    public function calculateSaleLift() {

        $averages_networks=[];
        $averages_networks_averages=[];
        foreach ($this->RsnNetworksLift->groupBy('name')->toArray() as $network) {
            foreach ($network as $value) {
                $averages_networks[$value['name']][] = $value['pivot']['resonance_score'];
            }
        }
      
      
        foreach ($averages_networks as $key => $network) {
            $averages_networks_averages[$key] = collect($network)->avg();
        }
     
        rsort($averages_networks_averages);
     
    
        $networksAverages = collect($averages_networks_averages);
        $A5 = $networksAverages->slice(0, 5)->avg();
     
        $B5 = $networksAverages->reverse()->slice(0, 5)->avg();
       
        $C5 = $this->NCS_LIFT[round($A5)];
        $D5 = $this->NCS_LIFT[round($B5)];
        
        $increment_5 = (($C5 / $D5) * 100 ) - 100;

        $A10 = $networksAverages->slice(0, 10)->avg();
        $B10 = $networksAverages->reverse()->slice(0, 10)->avg();

        $C10 = $this->NCS_LIFT[round($A10)];
        $D10 = $this->NCS_LIFT[round($B10)];

        $increment_10 = (($C10 / $D10) * 100 ) - 100;


        $A20 = $networksAverages->slice(0, 20)->avg();
        $B20 = $networksAverages->reverse()->slice(0, 20)->avg();

        $C20 = $this->NCS_LIFT[round($A20)];
        $D20 = $this->NCS_LIFT[round($B20)];

        $increment_20 = (($C20 / $D20) * 100 ) - 100;

        $AllNetAvg = $networksAverages->avg();

        $Davg = $this->NCS_LIFT[round($AllNetAvg)];

        $increment_average_5 = (($C5 / $Davg) * 100 ) - 100;
        $increment_average_10 = (($C10 / $Davg) * 100 ) - 100;
        $increment_average_20 = (($C20 / $Davg) * 100 ) - 100;


        return [
            'inc_5' => $increment_5,
            'inc_10' => $increment_10,
            'inc_20' => $increment_20,
            'inc_avr_5' => $increment_average_5,
            'inc_avr_10' => $increment_average_10,
            'inc_avr_20' => $increment_average_20
        ];

    }
}
