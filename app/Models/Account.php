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
        $name=$owner->first_name.'_'.$owner->last_name;
        $pass=Str::lower(Str::random(7));
        try{
            $acc=Account::create([
                'user_name'=>$name,
                'password'=>$pass,
                'owner_type'=> $is_emp ? Employee::class : Student::class,
                'owner_id'=>$owner->id
            ]);
        }catch(QueryException $e){
            abort(400,'failed to create the account!!'
            .',this username is already taken');
        }
        catch(Exception $e){
            abort(400,'something went wrong..'.$e->getMessage());
        }
        return [
            'user_name'=>$name,
            'password'=>$pass
        ];
    }
}
