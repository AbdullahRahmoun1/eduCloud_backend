<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Test;
class Type extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $fillable=['name'];
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

}
