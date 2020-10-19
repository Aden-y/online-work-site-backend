<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FreelancerDocuments extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\User', 'freelancer_id');
    }
}
