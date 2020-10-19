<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    public function bids()
    {
        return $this->hasMany('App\Bid', 'order_id');
    }

    public function freelancer()
    {
        return $this->belongsTo('App\User', 'freelancer_id');
    }

    public function client()
    {
        return $this->belongsTo('App\User', 'client_id');
    }

    public function files()
    {
        return $this->hasMany('App\OrderFile', 'order_id');
    }

    public function messages()
    {
        return $this->hasMany('App\OrderMessage', 'order_id');
    }

    public function skills()
    {
        return $this->hasMany('App\JobSkill', 'job_id');
    }

    public function submissions()
    {
        return $this->hasMany('App\OrderSubmission', 'order_id');
    }


    public function ratings() {
        return $this->hasMany('App\Rating', 'order_id');
    }






}
