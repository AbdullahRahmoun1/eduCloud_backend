<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtMark extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function student(){
        return $this->morphTo();
    }
    public function abilityTest(){
        return $this->belongsTo(AbilityTest::class);
    }
    public function sections()
    {
        return $this->hasMany(AtMarkSection::class);
    }

}
