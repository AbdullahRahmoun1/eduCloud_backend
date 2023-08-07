<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\Grade;
use App\Models\AtMark;
use App\Models\Number;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateStudent extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded = [];
    
    /**
     * Get the grade that owns the CandidateStudent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'owner');
    }

    public function numbers(): MorphMany
    {
        return $this->morphMany(Number::class, 'owner');
    }

    public function atMarks(): MorphMany
    {
        return $this->morphMany(AtMark::class, 'student');
    }
    public function full_name(){
        return $this->first_name.' '.$this->last_name;
    }
    public function canBecomeOfficial(){
        return Student::where('first_name',$this->first_name)
        ->where('last_name',$this->last_name)
        ->where('father_name',$this->father_name)
        ->where('mother_name',$this->mother_name)
        ->whereHas('g_class',fn($query)=>$query->where('grade_id',$this->grade_id))
        ->count()==0;
    }
    public function makeHimOfficial() {
        $attr= $this->getAttributes();
        unset($attr['created_at']);
        unset($attr['updated_at']);
        unset($attr['rejected']);
        unset($attr['reason']);
        unset($attr['id']);
        $s=Student::create($attr);
        $this->numbers()->update([
            'owner_id'=>$s->id,
            'owner_type'=>Student::class
        ]);
        $this->atMarks()->update([
            'student_id'=>$s->id,
            'student_type'=>Student::class
        ]);
        $this->notifications()->update([
            'owner_id'=>$s->id,
            'owner_type'=>Student::class
        ]);
        $this->delete();
        Helper::onlyKeepAttributes($s,[
            'id','grade_id','first_name','last_name'
        ]);
        return $s;
    }
    public function conversionDuplicateErrorMsg(){
        return "The conversion to an official student for {$this->full_name()}, ID: $this->id".
        " is not possible. Another student in the same grade"
        ." shares the same full name, mother's name, and father's name as this student. "
        ."Please edit the information of either this or the other student and try again.";
    }
    
    public function moveToOfficial($record,$student_id){

    }
}
