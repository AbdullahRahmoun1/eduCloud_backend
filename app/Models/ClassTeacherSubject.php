<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassTeacherSubject extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded=[];
    protected $table = 'class_teacher_subject';
    public static function joins() {
        return DB::table('class_teacher_subject AS cts')
            ->join('employees AS e','e.id','=','cts.employee_id')
            ->join('subjects AS s','s.id','=','cts.subject_id')
            ->join('g_classes AS c','c.id','=','cts.g_class_id');
    }
}
