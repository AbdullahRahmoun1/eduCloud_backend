<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
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
            res::error('you are not a supervisor of the class that took this test',code:403);
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

    public function editMark($mark_id){

        $mark = Mark::find($mark_id);
        if(!$mark){
            res::error('this mark id is not valid', code:422);
        }
        
        $class = $mark->test->g_class;
        //is this employee allowed to add marks for this class?
        if(Gate::denies('editClassInfo', [GClass::class, $class->id])){
            res::error('you are not a supervisor of this student',code:403);
        }

        $limit = $mark->test->max_mark;
        if(!request()->mark){
            res::error('the mark field is required', code:422);
        }
        if(request()->mark > $limit){
            res::error("the mark must not be greater than $limit");
        }

        $mark['mark'] = request()->mark;
        $mark->save();

        res::success('mark changed successfully',$mark->makeHidden('test'));
    }
    public function getRemainingStudents(Test $test, $abort = true){

        $g_class_id= $test->g_class_id;
        $students1 = Student::where('g_class_id',$g_class_id)->get();
        $students2 = Student::where('g_class_id',$g_class_id)
        ->whereHas('marks', function($query) use($test){
            $query->where('test_id',$test->id);
        })->get();

        $result = $students1->diff($students2);

        $result = $result->map(fn($item) => $item->only(['id', 'first_name', 'last_name', 'father_name', 'g_class_id']));

        if($abort)
            res::success('here are the students who\'s mark was\'t inserted yet:', $result);
        
        return $result;
    }

    public function getMarksOfStudent($student_id)
    {

        if($student_id <= -1){

            if(auth()->user()->owner_type != Student::class)
                res::error('invalid student id and you are not a student');
            else
                $student_id = auth()->user()->owner->id;
        }

        $student = Student::find($student_id);
        if(!$student){
            res::error('this student id is not valid', code:422);
        }

        Helper::tryToReadStudent($student_id, $abort = true);

        $tests = DB::table('tests')
            ->join('students', 'tests.g_class_id', '=', 'students.g_class_id')
            ->leftJoin('marks', function ($join) use ($student_id) {
                $join->on('tests.id', '=', 'marks.test_id')
                    ->where('marks.student_id', '=', $student_id);
            })
            ->join('types', 'tests.type_id', '=', 'types.id')
            ->join('subjects', 'tests.subject_id', '=', 'subjects.id')
            ->select('tests.id as test_id','tests.title as test_title','tests.date','types.id as type_id', 'types.name as type_name', 'subjects.id as subject_id', 'subjects.name as subject_name', 'tests.min_mark', 'tests.max_mark', 'marks.id as mark_id', 'marks.mark')
            ->where('students.id', $student_id)
            ->orderBy('tests.date')
            ->get();

            if(!$abort)
                return $tests;
            
            res::success('tests was brought successfully.', $tests);
    }
}
