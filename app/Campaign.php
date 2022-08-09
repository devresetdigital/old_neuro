<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Organization;

class Campaign extends Model
{

    use SoftDeletes;


    public function Strategies(){
        return $this->hasMany('App\Strategy');
    }
    public function Advertiser()
    {
        return $this->belongsTo('App\Advertiser');
    }
    public function CampaignsBudgetFlights(){
        return $this->hasMany('App\CampaignsBudgetFlight');
    }
    public function getGoalV1Attribute($value)
    {
        return round($value,2);
    }

    public function getFirstFlight(){
        $flights =$this->CampaignsBudgetFlights()->get();

        $start_date=false;
        foreach($flights as $flight) {
            if ($start_date==false) {
                $start_date = $flight->date_start;
            }
            if ($flight->date_start < $start_date) {
                $start_date = $flight->date_start;
            }
        }
    
        if ($start_date!=false) {
            return $start_date;
        }
       
        return '';
    }

    public function getLastFlight() {
        $flights = $this->CampaignsBudgetFlights()->get();

        $end_date = false;
        foreach($flights as $flight) {
            if ($end_date == false) {
                $end_date = $flight->date_end;
            }
            if ($flight->date_end > $end_date) {
                $end_date = $flight->date_end;
            }
        }

        if ($end_date!=false) {
            return $end_date;
        }

        return '';
    }

    /**
     * returns a date into the format mm-dd-yy
     */
    static function formatDates($date){
        $date = substr($date, 0,  10);
        if($date == '') return $date;
        $date = explode('-',$date);
        
        return $date[1] ."-". $date[2] ."-". substr($date[0], 2,  2);
    }
  



}
