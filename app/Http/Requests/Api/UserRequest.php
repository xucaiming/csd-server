<?php

namespace App\Http\Requests\Api;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules()
    {
//        switch($this->method()){
//            case 'POST': // 添加
//                return [
//                    'name' => 'required|between:3,25|unique:users',
//                    'email' =>'required|email|unique:users|max:50',
//                    // 'password' => 'required|confirmed|min:6',
//                ];
//            case 'PUT': // 修改
//                $userId = Auth::id();
//                return [
//                    'email' =>'required|email|max:50|unique:users,email,' . $userId,
//                    'name' => 'required|between:3,25|unique:users,name,' . $userId,
//                    // 'password' => 'required|confirmed|min:6',
//                ];
//        }
//        print_r($this); exit;
//        echo $this->email; exit;

        return [
            'email' => [
                'required',
                'email',
                'max:50',
                // Rule::exists('users')->whereNot('email', $this->email)
                'unique:users,email,' . $this->id,
            ],
            'name' => [
                'required',
                'between:3,25',
                'unique:users,name,' . $this->id,
                // Rule::exists('users')->whereNot('name', $this->name)
            ],
        ];

    }
}
