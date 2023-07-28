<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reply extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    public function employee() {
        return $this->belongsTo(Employee::class);
    }
    public function student() {
        return $this->belongsTo(Student::class);
    }
}
