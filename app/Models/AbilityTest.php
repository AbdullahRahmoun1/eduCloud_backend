<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mockery\Matcher\Subset;

class AbilityTest extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function sections()
    {
        return $this->hasMany(AtSection::class);
    }
    public function marks(){
        return $this->hasMany(AtMark::class);
    }
    
}
