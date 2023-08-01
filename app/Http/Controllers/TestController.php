<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\GClass;
use App\Models\Student;
use App\Models\Test;
use Illuminate\Http\Request;
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

        $grade = $class ? $class->grade_id : res::error('invalid class id',code:422);
        


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

        res::success('test created successfully', $test);
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

        res::success('test info updated successfully', $test);
    }
    
    public function test(Request $request)
    {   
        // $val = Validator::make($request, [
        //     'n' => [['required', 'max:7'], 'min:5'],
        //     'm' => 'integer|nullable'
        // ]);
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

    public function getTypeOfTest(Test $test){
        res::success(data:$test->type);
    }

    public function searchTests(Request $request){

        $request->validate([
            'subject_id' => 'exists:subjects,id',
            'g_class_id' => 'exists:g_classes,id',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'type_id' => 'exists:types,id',
        ]);
    
        $query = Test::query();
    
        $query->where('title', 'like', '%' . $request->title . '%');
    
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }
    
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
    
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
    
        if ($request->has('g_class_id')) {
            $query->where('g_class_id', $request->g_class_id);
        }
    
        $tests = $query->orderBy('date', 'desc')->simplePaginate(10);
    
        res::success('tests found successfully', $tests);
    }
}
