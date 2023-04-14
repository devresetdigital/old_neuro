<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rsn_x_two_items_domains extends Model
{
    protected $table = 'rsn_x_two_items_domains';

    protected $fillable = [
        'rsn_x_two_item_id',
        'domain_id',
        'score',
    ];

    public function domains()
    {
        return $this->belongsTo(Domains::class, 'domain_id', 'id');
    }

}
