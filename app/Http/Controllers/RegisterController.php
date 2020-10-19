<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\User;
use App\Address;
use App\FreelancerInformation;
use App\FreelancerDocuments;
use App\Skill;
use App\FreelancerSkill;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username'=> ['string','max:25', 'unique:users,username'],
            'email'=> ['email', 'unique:users,email'],
            'phonenumber'=> ['string', 'unique:users,phonenumber'],
            'nationalid'=> ['numeric', 'unique:users,nationalid'],
        ]);//->validate();
        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['errors'=>$errors], 400);
        }
       $user = User::create([
            'type' => $request->input('type'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'middlename' => $request->input('middlename'),
            'phonenumber' => $request->input('phonenumber'),
            'nationalid' => $request->input('nationalid'),
            'username' => $request->input('username'),
        ]);

       $address = $user->address()->save(new Address([
        'county' => $request->input('county'),
        'subcounty' => $request->input('subcounty'),
        'city' => $request->input('city'),
        'town' => $request->input('town'),
        'postaladdress' => $request->input('postaladdress'),
        'postalcode' => $request->input('postalcode'),
       ]));

      $exp = $request->input('experiencelevel');
      $exp_ = 1;
      if($exp == 'Beginer'){
          $exp_ = 1;
      }else if($exp == 'Intermediate'){
          $exp_ = 2;
      }else if($exp == 'Expert'){
        $exp_ = 3;
      }

      $freelancer_information =
      $user->freelancer_information()
      ->save(new FreelancerInformation([
            'experience_level'=>  $exp_,
            'education_level'=> $request->input('educationlevel'),
      ]));

      $resume = null;
      $certificate = null;

       if ($request->hasFile('certificate')) {
            $cert_docs = $request->file('certificate');
            $certificate = $user->id.'_ac_cert_.'.$cert_docs->extension();
            $cert_docs->move(public_path().'/uploads/certificates', $certificate);
       }

       if ($request->hasFile('resume')) {
        $res = $request->file('resume');
        $resume = $user->id.'_rsm_.'.$res->extension();
        $res->move(public_path().'/uploads/resumes', $resume);
       }

      $freelancer_documents =
      $user->freelancer_documents()
      ->save(new FreelancerDocuments([
            'resume'=> $resume,
            'academic_certificate'=>$certificate
      ]));


    $skills = $request->input('skills');
    $skills = json_decode($skills);

    foreach($skills as $skill){
        $skill = ltrim(rtrim(ucfirst($skill)));
       if(!$_skill = Skill::where('name', '=', $skill)->first()) {
        $_skill = Skill::create(['name'=> $skill]);
        $_skill->fresh();
       }
       $user->skills()->save(new FreelancerSkill([
           'skill_id' => $_skill->id
       ]));
    }

     $account = $user->account()->save(new Account([]));
     return response()->json(['message'=>'Registration successful.'], 200);
    }

    public function register_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'username'=> ['string','max:25', 'unique:users,username'],
            'email'=> ['email', 'unique:users,email'],
            'phonenumber'=> ['string', 'unique:users,phonenumber'],
            'nationalid'=> ['numeric', 'unique:users,nationalid'],
        ]);//->validate();

        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['errors'=>$errors], 400);
        }
       $user = User::create([
            'type' => $request->input('type'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'middlename' => $request->input('middlename'),
            'phonenumber' => $request->input('phonenumber'),
            'nationalid' => $request->input('nationalid'),
            'username' => $request->input('username'),
        ]);

       $address = $user->address()->save(new Address([
        'county' => $request->input('county'),
        'subcounty' => $request->input('subcounty'),
        'city' => $request->input('city'),
        'town' => $request->input('town'),
        'postaladdress' => $request->input('postaladdress'),
        'postalcode' => $request->input('postalcode'),
       ]));
     $account = $user->account()->save(new Account([]));

     return response()->json(['message'=>'Registration successful.'], 200);
    }
}
