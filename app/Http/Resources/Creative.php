<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class Creative extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //if date not in range status 0
        if(Carbon::now()->lt($this->start_date) && $this->start_date!=""){
            $this->status=0;
        }
        if(Carbon::now()->gt($this->end_date) && $this->end_date !=""){
            $this->status=0;
        }
        //DR Fou
        $drfou_tag = '<script src="https://api.b2c.com/api/init-266qo3kzencndtmrn2r.js?campaign={amas_campaign}&creative={amas_creative}&ssp={amas_ssp}&publisher={amas_publisher}&domain={amas_site}&appName={amas_app_name}&appDomain={amas_app_domain}&appID={amas_app_id}&appSite={amas_app_site}&SSP-PUBID-DOMAIN={amas_ssp}-{amas_publisher}-{amas_app_domain}&ip={amas_ip}&idfa={amas_device_ifa}" data-cfasync="false" async></script><noscript><img src="https://api.b2c.com/api/noscript-266qo3kzencndtmrn2r.gif"></noscript>';
        //Semcasting
        $semcasting_tag='<img src="https://bpi.rtactivate.com/tag/?id=20784&user_id={amas_device_ifa}" alt="" style="display:none !important;" />';
        //Semasio
        $semasio_tag ='<img src="https://uipus.semasio.net/reset/1/info?sType=sync&sExtCookieId={amas_device_ifa}&sInitiator=external" alt="" style="display:none !important;" />';
        //truoptik
        $truoptik = '<img src="https://dmp.truoptik.com/9922c1b0cbbaea8c/sync.gif?maid={amas_device_ifa}&dm={amas_site}&fck={amas_device_ifa}" style="display:none !important;"  />';
        //IX Pixel
        $ix_pixel='<img src="https://dsum-sec.casalemedia.com/rum?cm_dsp_id=197&external_user_id={amas_device_ifa}" style="display:none !important;"  />';
        //loopme
        $loopme_pixel='<img src="https://csync.loopme.me/?partner_id=2160&vt={amas_device_ifa}" style="display:none !important;"  />';
        //bidswitch cookie sync
        $bidswitch_pixel = '<img src="https://x.bidswitch.net/sync?dsp_id=447&user_id={rd_userid}&expires=90" style="display:none !important;"  /><img src="https://x.bidswitch.net/sync?ssp=resetdigital&user_id={rd_userid}&expires=90" style="display:none !important;"  />';


        //3rd party
        $thirdparty = isset($this->{'3rd_tracking'}) ? $this->{'3rd_tracking'} : "";


        $tag_code = isset($this->tag_code) ? preg_replace( "/\r|\n/", "", $this->tag_code.$drfou_tag.$semcasting_tag.$semasio_tag.$thirdparty.$truoptik.$ix_pixel.$loopme_pixel.$bidswitch_pixel) : "";

        $status = 0;

        if($_ENV['ENABLE_TMT_SCAN'] == 1){
                //CREATIVE IS ACTIVE ONLY IF IT'S SET AS ACTIVE AND THE SCAN STATUS iS 'LIVE' or 'PAUSED' 
                if($this->status == 1 && $this->TrustScan != null && $this->TrustScan->status=='LIVE'){
                    $status = 1;
                }
                if($this->status == 1 && $this->TrustScan != null && $this->TrustScan->status=='PAUSED'){
                    $status = 1;
                }
        } else {
            $status = $this->status;
        }

        //IF SCAN STATUS IS 'INCIDENT' MEANS THE CREATIVE IT'S 'BLOCKED' SO MUST NOT SHOW TAG CODE
        if($this->TrustScan != null && $this->TrustScan->status=='INCIDENT'){
            $tag_code='';
        }

        $response =  [
            "id" => $this->id,
            "creative_type_id" => $this->creative_type_id,
            "status" => $status,
            "advertiser_id" => $this->advertiser_id,
            "concept_id" => $this->concept_id,
            "name" => $this->name,
            "secure" => $this->secure,
            "click_url" => $this->click_url,
            "3pas_tag_id" => isset($this->{'3pas_tag_id'}) ? $this->{'3pas_tag_id'} : "",
            "landing_page" => $this->landing_page,
            "start_date" => isset($this->start_date) ? strtotime($this->start_date) : "",
            "end_date" => isset($this->end_date) ? strtotime($this->end_date) : "",
            "mime_type" => isset($this->mime_type) ? $this->mime_type : "",
            "mraid_required" => isset($this->mraid_required) ? $this->mraid_required : "",
            "tag_type" => isset($this->tag_type) ? $this->tag_type : "",
            "ad_format" => isset($this->ad_format) ? $this->ad_format : "",
            "ad_width" => isset($this->ad_width) ? $this->ad_width  : "",
            "ad_height" => isset($this->ad_height) ? $this->ad_height : "",
            "tag_code" => $tag_code,
            "3rd_tracking" => isset($this->{'3rd_tracking'}) ? $this->{'3rd_tracking'} : "",
            "vast_code" => isset($this->vast_code) ? $this->vast_code : "",
            "skippable" => isset($this->skippable) ? $this->skippable : "",
            "ad_serving_cost" => isset($this->ad_serving_cost) ? $this->ad_serving_cost : 0
        ];



        if($this->fields != null) {
            $comparative = $this->fields;
            $response = array_filter($response, function($k) use($comparative) {
                return in_array($k, $comparative);
            }, ARRAY_FILTER_USE_KEY);
        }

        return  $response;
   
    }
}
