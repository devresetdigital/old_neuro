<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class RsnXTwoItems extends Model
{
    protected $table='rsn_x_two_items';
    public $timestamps = false;

    public function RsnXTwoItemsData()
    {
        return $this->hasMany('App\RsnXTwoItemsData', 'item_id' , 'id');
    }
}
