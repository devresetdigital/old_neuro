<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class Vwi extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $filelink = json_decode($this->file,1);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "geolocation" => json_decode($this->geolocation),
            "start" => $this->start,
            "end" => $this->end,
            "days" => json_decode($this->days),
            "start_hour" => $this->start_hour,
            "end_hour" => $this->end_hour,
            "expiration" => $this->expiration,
            "created_at" => $this->created_at->getTimestamp(),
            "updated_at" => $this->updated_at->getTimestamp()
        ];
    }
}
