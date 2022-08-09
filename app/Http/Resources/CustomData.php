<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CustomData extends JsonResource
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
            "organization_id" => $this->organization_id,
            "file" => "http://".$_SERVER["HTTP_HOST"]."/storage"."/".$filelink[0]["download_link"],
            "created_at" => $this->created_at->getTimestamp(),
            "updated_at" => $this->updated_at->getTimestamp()
        ];
    }
}
