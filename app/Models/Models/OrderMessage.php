<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model
{
    protected $guarded = [];
    public function files() {
        return $this->hasMany('App/OrderMessageFile', 'order_message_id  ');
    }
}
