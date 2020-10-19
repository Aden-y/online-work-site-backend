<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'users';

    public function orders()
    {
        $this->hasMany('App\Order','client_id');
    }
}
