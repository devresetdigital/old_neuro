<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnCampaigns extends Model
{
    protected $table = 'rsn_campaigns';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function RsnAds(){
        return $this->hasMany('App\RsnAds', 'campaign_id');
    }

}
