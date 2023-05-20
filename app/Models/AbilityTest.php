<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbilityTest extends Model
{
    use HasFactory;
    public function subject()
    {
        //TODO :REPLACE WITH CLASS PATH
        return $this->belongsTo('subject');
    }
    public function sections()
    {
        return $this->hasMany(AtSection::class);
    }
}
