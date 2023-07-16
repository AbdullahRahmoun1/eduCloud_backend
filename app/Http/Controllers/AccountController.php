<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
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
                'exists:accounts,user_name',
                'required_if:anotherfield,value',                
            ],
            'password'=>[
                'required'
                ,'between:1,80'
            ],
        ]);
        $account=Account::where('user_name',$info['user_name'])
        ->first();
        
        if(!$account->passwordCheck($info['password']))
            return ResponseFormatter::error(null,'Wrong password',403);
        //abort(403,'Wrong password');
        $token=$account->createToken(request()->ip());
        return ResponseFormatter::success([
            'token'=>$token->plainTextToken,
            'roles'=>$account->owner->getRoleNames(),
        ], 'logged in successfully');
        // return [
        //     'token'=>$token->plainTextToken,
        //     'roles'=>$account->owner->getRoleNames(),
        // ];
    }
}
