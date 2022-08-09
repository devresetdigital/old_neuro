<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CreativeDisplay extends Model
{
    public $timestamps = false;
    protected $table = 'creatives_display';

    protected $fillable = ['creative_id','mime_type','mraid_required','tag_type','ad_format','ad_width','ad_height','tag_code','3rd_tracking'];
}
