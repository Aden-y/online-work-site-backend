<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

      // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'firstname', 'middlename','lastname', 'email', 'profilepicture', 'nationalid', 'idfront','idback'
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $guarded = [];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];






    public function address()
    {
        return $this->hasOne('App\Address', 'user_id');
    }





    public function account()
    {
        return $this->hasOne('App\Account', 'user_id');
    }

    public static function getClients(){
        return App\Models\Models\Freelancer::all();
    }

    public function messages()
    {
        return Message::where('source', $this->id)->orWhere('destination', $this->id)->get();
    }

    public function freelancer_documents(){
        if($this->type == 'Freelancer'){
            return $this->hasOne('App\FreelancerDocuments', 'freelancer_id');
        }
    }

    public function freelancer_information(){
        if($this->type == 'Freelancer'){
            return $this->hasOne('App\FreelancerInformation', 'freelancer_id');
        }
    }

    public function skills()
    {
        if($this->type == 'Freelancer'){
            return $this->hasMany('App\FreelancerSkill', 'freelancer_id');
        }
    }

    public function jobs()
    {
        if($this->type == 'Freelancer') {
            return $this->hasMany('App\Order', 'freelancer_id');
        }else if( $this->type == 'Client') {
            return $this->hasMany('App\Order', 'client_id');
        }
    }

    public function ratings() {
        return $this->hasMany('App\Rating', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification', 'user_id');
    }


}
