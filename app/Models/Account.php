<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Account extends User
{
    use HasApiTokens, HasFactory, Notifiable ,HasRoles;
    protected $hidden=[
        'password'
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
}
