<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtSection extends Model
{
    use HasFactory;
    public function abilityTest(){
        return $this->belongsTo(AbilityTest::class);
    }
    
}
