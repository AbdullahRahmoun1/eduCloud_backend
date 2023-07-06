<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model
{
    use HasFactory;
    protected $hidden =[
        'created_at',
        'updated_at'
    ];
    public function student(){
        return $this->belongsTo(Student::class);
    }
}
