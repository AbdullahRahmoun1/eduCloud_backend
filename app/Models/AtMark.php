<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtMark extends Model
{
    use HasFactory;
    public function student(){
        return $this->morphTo();
    }
    public function abilityTest(){
        return $this->belongsTo(AbilityTest::class);
    }

}
