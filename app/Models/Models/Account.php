<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    protected $guarded = [];

    public function user()
    {
      return $this->belongsTo('App\User', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'account_id');
    }





}
