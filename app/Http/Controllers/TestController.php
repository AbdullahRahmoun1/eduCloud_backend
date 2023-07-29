<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as response;
use App\Models\GClass;
use App\Models\Student;
use App\Models\Test;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TestController extends Controller
{

    private function validateTestInfo(){

        $this->authorize('editClassInfo', [request()['g_class_id']]);
        $emp = request()->user()->owner;
        $supHasClass = Rule::exists('class_supervisor','g_class_id')
        ->where(function($query) use ($emp){
            $query->where('employee_id',$emp->id);
        });

        if(in_array('principal',$emp->get_roles()->toArray())){
            $supHasClass = 'exists:g_classes,id';
        }

        $class = GClass::find(request()->g_class_id);

        $grade = $class ? $class->grade_id : response::error('invalid class id',code:422);
        


        $correctSubject = Rule::exists('subjects','id')->where(function($query) use ($grade) {
            $query->where('id',request()->subject_id)->where('grade_id',$grade);
        });

        $unique = Rule::unique('tests','title')->where(function($query){
            $query->where('subject_id',request()->subject_id)
            ->where('g_class_id', request()->g_class_id);
        });

        $data = request()->validate([
            'title' => ['required', 'min:2', 'max:25', $unique],
            'image_url' => 'required',
            'min_mark' => ['required', 'numeric', 'gt:0'],
            'max_mark' => ['required', 'numeric', 'gt:min_mark'],
            'date' => ['required', 'date'],
            'subject_id' => ['required', $correctSubject],
            'g_class_id' => ['required', $supHasClass],
            'type_id' => ['required', 'exists:types,id'],
            'progress_calendar_id' => ['exists:progress_calendars,id', 'unique:tests'],
        ] ,[
            'title.unique' => 'this title is already in use for this subject and class',
            'g_class_id.exists' => 'this class id is invalid or this employee does not supervise it',
            'subject_id.exists' => 'this class is not studying this subject'
        ]);

        return $data;
    }

    public function add(){

        $data = $this->validateTestInfo();

        $test = Test::create($data);

        response::success('test created successfully', $test);
    }

    public function edit(Test $test){

        $unique = Rule::unique('tests','title')->where(function($query){
            $query->where('subject_id',request()->subject_id)
            ->where('g_class_id', request()->g_class_id);
        });

        $data = request()->validate([
            'title' => ['required', 'min:2', 'max:25', $unique],
            'image_url' => 'required',
            'min_mark' => ['required', 'numeric', 'gt:0'],
            'max_mark' => ['required', 'numeric', 'gt:min_mark'],
            'date' => ['required', 'date'],
            'type_id' => ['required', 'exists:types,id'],
            'progress_calendar_id' => ['exists:progress_calendars,id', 'unique:tests'],
        ]);

        $test->update(array_diff_key($data,['subject_id' => '', 'g_class_id' => '']));

        response::success('test info updated successfully', $test);
    }
    
    public function test(Request $request)
    {   
        // $val = Validator::make($request, [
        //     'n' => [['required', 'max:7'], 'min:5'],
        //     'm' => 'integer|nullable'
        // ]);
        return ( Student::find(1)->grade);
        if(Gate::denies('editClassInfo',[Test::class,3]))
        return 'hhhh';
        return ( request()->user()->owner->roles()->select('id')->get()->makeHidden('pivot'));
    }

    public function getTestMarks(Test $test){
        Helper::tryToRead($test->g_class_id);
        $marks= $test->marks;
        $marks->load([
            'student:id,first_name,last_name',
        ]);
        $marks->makeHidden([
            'test_id','student_id',
            'student.first_name',
            'student.last_name'
        ]);
        foreach($marks as $mark){
            $student=$mark->student;
            $student->full_name =$student->first_name.
            ' '.$student->last_name;
            $student->makeHidden([
                'first_name',
                'last_name'
            ]);
        }
        return $marks;
    }
}
