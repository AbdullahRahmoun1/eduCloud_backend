<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function login()
    {
        $info=request()->validate([
            'user_name'=>[
                'required',
                'between:1,80',
                'exists:accounts,user_name'
            ],
            'password'=>[
                'required'
                ,'between:1,80'
            ],
        ]);
        $account=Account::where('user_name',$info['user_name'])
        ->first();
        if(!$account->passwordCheck($info['password']))
        abort(403,'wrong password');
        $token=$account->createToken(request()->ip());
        return [
            'token'=>$token->plainTextToken,
            'roles'=>$account->getRoleNames(),
            'permissions'=>$account->permissions
        ];
    }
}
