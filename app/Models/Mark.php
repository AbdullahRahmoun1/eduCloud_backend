<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
