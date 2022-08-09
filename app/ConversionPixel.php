<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ConversionPixel extends Model
{
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }

    public function Campaign(){
        return $this->belongsToMany('App\Campaign' ,'campaing_conversion_pixel', 
        'conversion_pixel_id', 'campaign_id');
    }
}
