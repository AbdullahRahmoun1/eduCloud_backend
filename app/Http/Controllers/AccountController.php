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
                'between:2,45',
                'exists:accounts,user_name',
                'required_if:anotherfield,value',                
            ],
            'password'=>[
                'required'
                ,'between:5,45'
            ],
        ]);
        $account=Account::where('user_name',$info['user_name'])
        ->first();
        
        if(!$account->passwordCheck($info['password']))
            return ResponseFormatter::error('Wrong password', null, 403);

        $token=$account->createToken(request()->ip());
        return ResponseFormatter::success('logged in successfully', [
            'token'=>$token->plainTextToken,
            'roles'=>$account->owner->getRoleNames(),
        ]);
    }
}
