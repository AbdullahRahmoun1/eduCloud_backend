<?php

namespace App\Models;

use App\Helpers\ResponseFormatter;
use Exception;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;

class Account extends User
{
    use HasApiTokens, HasFactory, Notifiable ;
    protected $hidden=[
        'password',
        'created_at',
        'updated_at'
    ];
    protected $guarded = [];
    public function setPasswordAttribute($password){
        $this->attributes['password']=bcrypt($password);
    }
    public function passwordCheck($pass):bool {
        return Hash::check($pass, $this->password);
    }
    public function owner(){
        return $this->morphTo();
    }
    public static function createAccount($owner, $is_emp){
        $suffix = sprintf('%04d', random_int(0,9999));
        $name=$owner->first_name.'_'.$owner->last_name.'_'.$suffix;
        $pass=Str::lower(Str::random(7));
        try{
            $acc=Account::create([
                'user_name'=>$name,
                'password'=>$pass,
                'owner_type'=> $is_emp ? Employee::class : Student::class,
                'owner_id'=>$owner->id
            ]);
        }catch(QueryException $e){
            $code = $e->errorInfo[1];
            if($code[1]==1062)
            return Account::createAccount($owner,$is_emp);
            else throw new Exception('Something went wrong in creating account..INFO: '
            .$e->getMessage());
        }

        return [
            'user_name'=>$name,
            'password'=>$pass
        ];
    }

    public static function changePassword(Account $account, $new_pass = null){

        if(!isset($new_pass)){
            $new_pass = Str::lower(Str::random(7));
        }

        $len = Str::length($new_pass);
        if($len > 45 || $len < 5){
            throw new Exception('password must be less than 45 character and at least 5 character');
        }
        
        $account->update(['password' => $new_pass]);
        return $new_pass;
    }
}
