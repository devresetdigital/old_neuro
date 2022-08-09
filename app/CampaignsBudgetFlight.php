<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignsBudgetFlight extends Model
{
    public $timestamps = false;
    protected $fillable = ['campaign_id', 'date_start', 'date_end', 'budget', 'impression'];

    protected $casts = [
        'date_start' => 'datetime:m-d-Y',
        'date_end' => 'datetime:m-d-Y',
    ];
}
