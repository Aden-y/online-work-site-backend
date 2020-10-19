<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.username'=> 'present|required|not_in:admin,test,user,username|unique:accounts,username',
            'user.password' => 'present|required|regex:^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$',
            'user.firstname'=>'present|required',
            'user.middlename'=>'present|nullable',
            'user.lastname'=>'present|required',
            'user.idnumber'=>'present|required|unique:users,nationalid',
            'user.email'=>'present|required|email|unique:users,email',
            'user.phonenumber'=>'present|required|unique:users,phonenumber',
            'user.educationlevel.level'=>'present|required',
            'user.educationlevel.certificate'=>'present|required_if:user.educationlevel.level,Primary, Secondary
            ,Certificate,Diploma,Degree,PhD',
            'user.experiencelevel.level'=>'present|required',
            'user.experiencelevel.docs'=>'present|required_if:user.experiencelevel.level:',
            'user.address.county'=>'present|reqired|string',
            'user.address.subcounty'=>'present|reqired|string',
            'user.address.city'=>'present|reqired|string',
            'user.address.town'=>'present|reqired|string',
            'user.address.county'=>'present|reqired|string',
            'user.address.postalcode'=>'present|reqired|number',
            'user.address.postaladdress'=>'present|reqired|string',
            'user.skills'=>'present|required'


        ];
    }

    /**
 * Get the error messages for the defined validation rules.
 *
 * @return array
 */
public function messages()
{
    $required = ':attribute is required';
    return [
        'username.required' => $required,
        'password.required'  => $required,
        'username.not_in' => 'Th username can not be used',

    ];
}

/**
 * Get custom attributes for validator errors.
 *
 * @return array
 */
public function attributes()
{
    return [
        'username' => 'account username',
    ];
}


}
