<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreativeVideo extends Model
{
    public $timestamps = false;
    protected $table = 'creatives_video';

    protected $fillable = ['creative_id','vast_code','skippable', 'duration', 'bitrate', 'vast_type'];
}
