<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\ResponseFormatter as res;
use App\Models\AtMark;
use App\Models\GClass;
use App\Models\Mark;
use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    public function addTestMarks(Test $test){

        $class = $test->g_class;
        $max_mark = $test->max_mark;
        //is this employee allowed to add marks for this class?
        if(Gate::denies('editClassInfo', [GClass::class, $class->id])){
            res::error('you are not a supervisor of the class that took this test',403);
        }
        
        $data = request()->validate([
            '*.student_id' => ['required','exists:students,id'],
            '*.mark' => ['required', 'numeric', 'gt:-1', "lte:$max_mark"]
        ]);

        DB::beginTransaction();
        $entryNum = 0;
        $finlaResult = [];
        foreach($data as $entry){
            
            $entryNum++;
            
            //does this student belong to the class that took the test?
            $student = Student::find($entry['student_id']);
            if($student->g_class_id != $class->id){
                DB::rollBack();
                res::error("error in entry $entryNum ... this student does not belong to the class that took the test",code:422);
            }
            
            //is this entry duplicated?
            $unique = Mark::where('student_id',$student->id)
            ->where('test_id',$test->id)->first();
            if(isset($unique)){
                DB::rollBack();
                res::error("error in entry $entryNum ... this entry is duplicated");
            }

            //everything good... now create
            try{
                $studentMark = Mark::create(array_merge($entry,['test_id' => $test->id]));
            }
            catch(Exception $e){
                res::queryError($e,rollback:true);
            }
            
            $studentMark['student_name'] = $student['first_name'].' '.$student['last_name'];
            
            $finlaResult[] = $studentMark;
        }

        DB::commit();
        res::success('marks added successfully', $finlaResult);
    }

    public function getRemainingStudents(Test $test){

        $g_class_id= $test->g_class_id;
        $students = Student::where('g_class_id',$g_class_id)->with('g_class:id,name,grade_id', 'g_class.grade:id,name')->select('id','g_class_id')->get();
        return $students;
        $students = array_map(fn($s)=>[
            'id' => $s['id'],
            'full_name' => $s['first_name'].' '.$s['last_name'],

        ],$students);
    }
}
