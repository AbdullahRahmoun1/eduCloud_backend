<?php

namespace App\Models;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Test extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    protected $appends = ['avg'];
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function g_class()
    {
        return $this->class();
    }

    public function class()
    {
        return $this->belongsTo(GClass::class,'g_class_id');
    }
    public function baseCalender()
    {
        return $this->belongsTo(ProgressCalendar::class);
    }
    public function marks(){
        return $this->hasMany(Mark::class);
    }
    
    public function getAvgAttribute(){

        $marks = $this->marks;
        $sum = 0;
        $count = count($marks);

        if($count == 0){
            return -1;
        }

        foreach($marks as $mark){
            $sum += $mark->mark;
        }

        $result = $sum/$count;

        return number_format($result, 2);
    }
}
