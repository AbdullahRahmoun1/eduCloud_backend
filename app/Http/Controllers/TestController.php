<?php

namespace App\Http\Controllers;

use App\Events\PrivateNotification;
use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\GClass;
use App\Models\Student;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\MarkController;
use Exception;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{

    private function validateTestInfo(){

        if(Gate::denies('editClassInfo',[GClass::class, request()['g_class_id']])){
            res::error('you are not a supervisor of this class', code:403);
        }

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
            'image' => ['image', 'mimes:jpeg,png,jpg,', 'max:2048'],
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

        $data['image'] = request()->file('image');
        return $data;
    }

    public function add(){

        $data = $this->validateTestInfo();

        $test = Helper::lazyQueryTry(fn()=>Test::create($data));

        if(isset($data['image'])){
            $image = $data['image'];
            $fileName = $test->id.('.').$image->getClientOriginalExtension();
            $image->storeAs('public/images', $fileName);

            $test->image_url = "storage/images/$fileName";
            $test->save();
        }
        res::success('test created successfully', $test);
    }

    public function edit(Test $test){

        if(Gate::denies('editClassInfo',[GClass::class, request()['g_class_id']])){
            res::error('you are not a supervisor of the class that took this test',code:403);
        }
        
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
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $image=request()->file('image');
        $fileName = request()->num . '.' .$image->getClientOriginalExtension();
        $image->storeAs('public/images', $fileName);
        $contents = Storage::get("public/images/$fileName");

        return $contents;
    }
    

    public function getTestMarks(Test $test, $abort = true){
        Helper::tryToRead($test->g_class_id);
        $marks= $test->marks;
        $marks->load([
            'student:id,first_name,last_name,father_name,mother_name,grade_id,g_class_id',
        ]);
        $marks->makeHidden([
            'test_id','student_id',
            'student.first_name',
            'student.last_name'
        ]);
        
        if($abort)
            res::success(data:$marks);

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
        
        if($request->has('page'))
            $tests = $query->orderBy('date', 'desc')->simplePaginate(10);
        else
            $tests = $query->orderBy('date', 'desc')->get();
        // return $tests;
        $tests->transform(function ($test) {

            $mark_controller = new MarkController();
            $remaining = $mark_controller->getRemainingStudents($test, false);

            $test['all_marks_inserted'] = $remaining->isEmpty() ? true : false;
            return $test;
        });
        
        res::success('tests found successfully', $tests);
    }

    public function getTest($id)
    {
        $test = Test::with(['subject:id,name,grade_id', 'g_class:id,name', 'type:id,name'])->find($id);
    
        if ($test === null) {
            res::error('this test id is not valid', code:404);
        }
        
        if(Gate::denies('viewClassInfo',[GClass::class,$test->g_class->id]))
            res::error("You dont have the permission to read this test info.",code:403);
        
        $test->makeHidden(['subject_id', 'g_class_id', 'type_id']);

        $mark_controller = new MarkController();
        $remaining = $mark_controller->getRemainingStudents($test, false);
        
        $test['all_marks_inserted'] = $remaining->isEmpty() ? true : false;
        $test['grade'] = $test->subject->grade;

        self::getTestMarks($test,false);

        res::success(data:$test);
    }
}
