<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreativeAudio extends Model
{
    public $timestamps = false;
    protected $table = 'creatives_audio';

    protected $fillable = ['creative_id','audio_file'];
}
