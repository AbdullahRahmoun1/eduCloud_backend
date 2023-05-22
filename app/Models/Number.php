<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function owner(){
        return $this->morphTo();
    }
    //TODO : REPLACE 'CANDIDATE' AND 'STUDENT' WITH ACTUALL CLASS PATHS
    public static function addNumbersToStudent($nums,$student_id,$studentType='candidate'){
        $nums=array_map(fn($number)=>[
            'number'=>$number,
            'owner_id'=>$student_id,
            'owner_type'=>$studentType,
            'created_at'=>now(),
            'updated_at'=>now(),
        ],$nums);
        Number::insert($nums);
    }
}
