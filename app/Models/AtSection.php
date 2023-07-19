<?php

namespace App\Models;

use App\Models\AbilityTest;
use App\Models\AtMarkSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtSection extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    public function abilityTest(){
        return $this->belongsTo(AbilityTest::class);
    }
    public function marks()
    {
        return $this->hasMany(AtMarkSection::class);
    }
}
