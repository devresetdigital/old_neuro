<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class Concept extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        //creatives
        $creatives_list=array();
        foreach ($this->creatives as $creative) {
            if ($creative->status == 1) {
                $creatives_list[] = $creative->id;
             }
        }
        

        $response = [
            "id" => $this->id,
            "name" => $this->name,
            "advertiser_id" => $this->advertiser_id,
            "created_at" => $this->created_at->getTimestamp(),
            "updated_at" => $this->updated_at->getTimestamp(),
            "creatives" => $creatives_list
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
