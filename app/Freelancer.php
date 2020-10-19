<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    protected $table = 'users';
    public function oders()
    {
        return $this->hasMany('App\Order', 'freelancer_id');
    }

    public function information()
    {
        return $this->hasOne('App\FreelancerInformation', 'freelancer_id');
    }

    public function skills(){
        return $this->hasMany('App\FreelancerSkills', 'freelancer_id');
    }

    public function docs()
    {
        return $this->hasMany('App\FreelancerDocments', 'freelancer_id');
    }


    public function loadOrders() 
    {
        $orders = Order::where([
            ['status', '=', 'Unassigned'],
            ['rating_required', '<=', $this->rating],
            ['experience_required', '<=', $this->experience_level]
            ])->get();
    }



}
