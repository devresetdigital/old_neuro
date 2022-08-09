<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnNeedstates extends Model
{
    protected $table = 'rsn_need_state';
    public $timestamps = false;
    protected $fillable = ['name', 'type'];
}
