<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentBus extends Model
{
    use HasFactory;
    protected $table='student_bus';
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];

    public static function joins(){
        return DB::table('student_bus as sb')
        ->join('students as s','s.id','=','sb.student_id')
        ->join('buses as b','b.id','=','sb.bus_id');
    }
}
