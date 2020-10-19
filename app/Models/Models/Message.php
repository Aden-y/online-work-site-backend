<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
    public function files() {
        return $this->hasMany('App/MessageFile', 'message_id ');
    }
}
