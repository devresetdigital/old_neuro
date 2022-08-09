<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Vwi extends Model
{
    public function Organization(){
        return $this->belongsTo('App\Organization');
    }
    protected $fillable = ['vwi_locations_id','name','geolocation','start','end','days','start_hour','end_hour','expiration'];
}
