<?php

namespace App\Http\Controllers;

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

        $data = request()->validate([
            'title' => ['required', 'min:2', 'max:25'],
            'image_url' => 'required',
            'min_mark' => ['required', 'numeric', 'gt:0'],
            'max_mark' => ['required', 'numeric', 'gt:min_mark'],
            'date' => ['required', 'date'],
            'subject_id' => ['required', $correctSubject],
            'g_class_id' => ['required', $supHasClass],
            'type_id' => ['required', 'exists:types,id'],
            'progress_calendar_id' => ['exists:progress_calendars,id', 'unique:tests'],
        ] ,[
            'class_id.exists' => 'this class id is invalid',
            'subject_id.exists' => 'this class is not studying this subject'
        ]);

        return $data;
    }

    public function add(){

        $data = $this->validateTestInfo();

        Test::create($data);

        response::success('test created successfully', $data);
    }

    public function edit(Test $test){
        $data = $this->validateTestInfo();

        $test->update($data);

        response::success('test info updated successfully', $data);
    }
    
    public function test(Request $request)
    {   
        $val = Validator::make($request, [
            'n' => [['required', 'max:7'], 'min:5'],
            'm' => 'integer|nullable'
        ]);
        
        return response::error(null, 'shit' , 512);
    }
}
