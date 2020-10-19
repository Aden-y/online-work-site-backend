<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $guarded = [];
    public function order()
    {
        return $this->belongsTo('App\Order','order_id');
    }

    public function freelancer()
    {
        return $this->belongsTo('App\User','freelancer_id');
    }

    public function get_freelancer_name() {
        return $this->freelancer->firstname.' '.$this->freelancer->middlename.' '.$this->freelancer->lastname;
    }
}
