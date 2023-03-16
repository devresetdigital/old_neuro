<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnSignalCampaign extends Model
{
  protected $fillable = [
    'name', 'organization_id', 'advertiser_id', 'file_path', 'type', 'assets'
  ];
}
