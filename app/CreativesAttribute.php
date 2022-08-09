<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CreativesAttribute extends Model
{
    public $timestamps = false;
    protected $fillable = ['creative_id','attribute_id'];
}
