<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Absence;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as res;

class AbsenceController extends Controller
{
    public function addAbsences(){
     //Input validation..
        $data=request()->validate([
            'class_id'=>['required','exists:g_classes,id'],
            'absences.*.student_id'=>['required','exists:students,id'],
            'absences.*.justification'=>['string','between:1,100']
        ]);
     //Does this user have the permission
        Helper::tryToEdit($data['class_id']);
     //Custom input validation
        foreach($data['absences'] as $abs){
            //Are all of the student belong to this class
            $student=Student::find($abs['student_id']);
            if($student->g_class_id!=$data['class_id'])
                res::error("Student {$student->full_name()} (num:$student->id) doesn't belong to this class.");
        }
     //Insert to DB
        DB::beginTransaction();
        $ctr=0;
        try{
            foreach($data['absences'] as $abs){
                $abs['date']=now();
                $abs['justified']=isset($abs['justification']);
                Absence::create($abs);
                $ctr++;
            }
        }catch(QueryException $e){
            $student=Student::find($data['absences'][$ctr]['student_id']);
            $dupMsg="Student {$student->full_name()} (num:$student->id) ".
            "has already been marked as absent for today or is entered twice.";
            res::queryError($e,$dupMsg,rollback:true);
        }
        //TODO: send a notification to parents
        res::success(commit:true);
    }
    public function studentAbsences($student_id){
        if($student_id<=0){
            $owner=request()->user()->owner;
            if(!$owner instanceof Student)
            res::error("You can't get your absences when you are an employee.",code:422);
            $student_id=request()->user()->owner->id;
        }
        $student=Student::find($student_id);
        if(!$student){
            res::error("Student not found.",code:404);
        }
        Helper::tryToReadStudent($student_id);
        return $student->absences;
    }
    public function justifyAbsence(Absence $absence) {
        return public_path();
        $data=request()->validate([
            'justification'=>['required','string','between:1,100']
        ]);
        Helper::tryToEdit($absence->student->g_class_id);
        if($absence->justified)
        res::error("This absence is already justified, Try editing the justification");
        $data['justified']=true;
        $absence->update($data);
        res::success();
        
    }

    public function editJustification(Absence $absence) {
        $data=request()->validate([
            'justification'=>['required','string','between:1,100']
        ]);
        Helper::tryToEdit($absence->student->g_class_id);
        $absence->update($data);
        res::success();
    }
}
