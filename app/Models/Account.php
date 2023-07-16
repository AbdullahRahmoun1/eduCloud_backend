<?php

namespace App\Models;

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
        'created_at'
        ,'updated_at'
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
<<<<<<< HEAD
            $info=$e->errorInfo;
            if($info[1]==1062)
=======
            $code = $e->errorInfo[1];
            if($code[1]==1062)
>>>>>>> 0df10059e283f898ffdda5383e0381b3c3c7b93c
            return Account::createAccount($owner,$is_emp);
            else throw new Exception('Something went wrong in creating account..INFO: '
            .$e->getMessage());
        }

        return [
            'user_name'=>$name,
            'password'=>$pass
        ];
    }
}
