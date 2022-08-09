<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnNetworks extends Model
{
    protected $table = 'rsn_networks';
    public $timestamps = false;
    protected $fillable = ['name'];
}
