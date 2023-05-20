<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtSection extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function abilityTest(){
        return $this->belongsTo(AbilityTest::class);
    }
    public function marks()
    {
        return $this->hasMany(AtMarkSection::class);
    }
}
