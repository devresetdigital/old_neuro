<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\CreativeDisplay;
use App\CreativeVideo;
use App\CreativesAttribute;

use App\Concept;
use App\Campaign;
use Illuminate\Support\Carbon;

class Creative extends Model
{
    use SoftDeletes;

    
    public function Advertiser()
    {
        return $this->belongsTo('App\Advertiser');
    }
    public function CreativeAttributes(){
        return $this->hasMany('App\CreativesAttribute');
    }

    public function CreativeDisplays(){
        return $this->hasMany('App\CreativeDisplay');
    }

    public function CreativeLanguages(){
        return $this->hasMany('App\CreativeLanguage');
    }

    public function CreativeVideos(){
        return $this->hasMany('App\CreativeVideo');
    }

    public function Concept(){
        return $this->belongsTo('App\Concept');
    }

    public function TrustScan(){
        return $this->hasOne('App\TrustScan');
    }

    /**
     * 
     */
    static function getDataToExport($id){

        $creative =self::find($id);
   
        $creative->ad_height = '';
        $creative->ad_width = '';
        $creative->tag_code ='';
        $creative->vast_code = '';
        $creative->skippable = '';
        
        if($creative->creative_type_id == 1){
            $creative_display = CreativeDisplay::where('creative_id', $creative->id)->first();
            $creative->ad_height = $creative_display->ad_height;
            $creative->ad_width = $creative_display->ad_width;
            $creative->tag_code = $creative_display->tag_code;
        }
        if($creative->creative_type_id == 2){
            $creative_display = CreativeVideo::where('creative_id', $creative->id)->first();
            $creative->vast_code = $creative_display->vast_code;
            $creative->skippable = $creative_display->skippable;
        }
        $attributes=[];
        foreach(CreativesAttribute::where('creative_id', $creative->id)->get() as $attribute ) {
            $attributes[]=$attribute->attribute_id;
        }
        $creative->creative_attributes = implode(',', $attributes);

        $start =  substr($creative->start_date,0,10);
        $end =  substr($creative->end_date,0,10);

        if($start != '') {
            $start = explode("-", $start);
            $start = $start[1] ."-". $start[2] ."-". $start[0];
        }
        if($end != '') {
            $end = explode("-", $end);
            $end = $end[1] ."-". $end[2] ."-". $end[0];
        }
      

        return  array(array(
            $creative->id,
            $creative->creative_type_id,
            $creative->name,
            $creative->click_url,
            $creative->{'3pas_tag_id'},
            $creative->landing_page,
            $start,
            $end,
            $creative->concept_id,
            $creative->ad_height,
            $creative->ad_width,
            $creative->tag_code,
            $creative->{'3rd_tracking'},
            $creative->creative_attributes,
            $creative->vast_code,
            $creative->skippable
        ));
        
    }


    static function updateTimestamp($id){

        $creative =self::find($id);
        $creative->updated_at = Carbon::now();
        $creative->save();

        $updated_at = $creative->updated_at;

        $concept = Concept::find($creative->concept_id);

        if($concept == null){
            return false;
        }

        $concept->updated_at = $updated_at;
        $concept->save();

        foreach($concept->Strategies as $strategy){
            $strategy->updated_at = $updated_at;
            $strategy->save();

            $campaign = Campaign::find(intval($strategy->campaign_id));
            if($campaign){
                $campaign->updated_at = $updated_at;
                $campaign->save();
            }
        }
    }

}
