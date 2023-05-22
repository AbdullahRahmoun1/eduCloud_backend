<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function notifications(){
        return $this->hasMany(Notification::class);
    }
}
