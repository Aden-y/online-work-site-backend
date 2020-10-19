<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobSkill extends Model
{
    protected $guarded = [];
    public  $timestamps = false;

    public function skill() {
        return $this->hasOne('App\Skill', 'skill_id');
    }
}
