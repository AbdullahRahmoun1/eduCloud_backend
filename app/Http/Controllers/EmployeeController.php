<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Exception;
use App\Models\Account;
use App\Models\Employee;
use App\Models\ClassSupervisor;
use App\Models\ClassTeacherSubject;
use App\Models\GClass;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Help;

use function PHPUnit\Framework\isEmpty;

class EmployeeController extends Controller
{
    
    public function add(){
        //Validation
        $f=['required','string','between:5,25'];
        $l=$f;
        $l[]=Rule::unique('employees','last_name')
        ->where('first_name',request('first_name'));
        $data=request()->validate([
            'first_name'=>$f,
            'last_name'=>$l,
            'roles'=>['required','array','between:1,4'],
        ],[
            'last_name.unique'=>'This employee is already in the system'
        ]);
        //check if roles are valid
        foreach($data['roles'] as $role)
            if(config('roles.'.$role,-1)==-1)
                abort(422,"Role $role isn't an actual role..");
        //create the employee
        $emp=Employee::create($data);
        //assign the roles to him
        array_map(fn($role)=>$emp->assignRole($role)
        ,$data['roles']);
        //now create an account for him :)  
        $acc=Account::createAccount($emp, 1);
        //.................
        return [
            'message'=>'Employee was added successfully',
            'data' => $acc
        ];

    }
    public function edit(Employee $employee){
     //Validation
        $l=Rule::unique('employees','last_name')
        ->where('first_name',request('first_name'));
        $data=request()->validate([
            'first_name'=>['required','string','between:5,25'],
            'last_name'=>['required_with:first_name','string','between:5,25',$l],
        ],[
            'last_name.unique'=>'Unable to update employee information.'.
            ' This employee\'s name already exists in the system.'
        ]);
        //TODO: If we added more attributes..handle them here
     //Update:
        Helper::lazyQueryTry(
            fn()=>$employee->update($data)
        );
        return Helper::success();
    }
    public function assignClassesToSupervisor(Employee $sup){
     //Validation
        $data=request()->validate([
            'classes'=>['required','array','min:1'],
            'classes.*'=>['numeric','exists:g_classes,id'],
            'supervisor_id'=>['required','numeric','exists:employees,id']
        ]);
     //Is he a supervisor?
        if(!$sup->hasRole(config('roles.supervisor')))
        abort(422,'This employee isn\'t a supervisor');
     //Assign Classes to him in the DB
        $ctr=0;
        DB::beginTransaction();
        try{
            foreach($data['classes'] as $class){
                ClassSupervisor::create([
                    'g_class_id'=>$class,
                    'employee_id'=>$sup->id
                ]);
                $ctr++;
            }
        }catch(QueryException $e){
            $code=$e->errorInfo[1];
            if($code==1062){
                $msg="The employee is already assigned as ".
                "a supervisor for the class with ID : {$data['classes'][$ctr]}";
            }else{
                $msg='Something went Wrong...error:'.$e->getMessage();
            }
            DB::rollBack();
            abort(400,$msg);
        }
     //Success!!, return message   
        DB::commit();
        return [
            'message'=>'Success!'
        ];
    }
    public function assign_Class_Subject_ToTeacher(Employee $teacher) {
     //Validation
        $data=request()->validate([
            '*'=>['required','array','between:2,2'],
            '*.subject_id'=>['required','numeric','exists:subjects,id'],
            '*.classes'=>['min:1','required','array'],
            '*.classes.*'=>['required','numeric','exists:g_classes,id'],
        ]);
        if(empty(request()->all()))
        abort(422,'wth?..Empty body');
     //Is he a teacher?
        if(!$teacher->hasRole(config('roles.teacher')))
        abort(422,'This employee isn\'t a teacher');
     //Assign values to DB
        DB::beginTransaction();
        $it=0;
        $cls=0;
        try{
            foreach($data as $item){
                foreach($item['classes'] as $class){
                    $cls++;
                    ClassTeacherSubject::create([
                        'employee_id'=>$teacher->id,
                        'subject_id'=>$item['subject_id'],
                        'g_class_id'=>$class
                    ]);
                }
                $cls=0;
                $it++;
            }
        }catch(QueryException $e){
     //Error :( , return response
            $code=$e->errorInfo[1];
            $sub=Subject::find($data[$it]['subject_id'])->name;
            $class =GClass::find($data[$it]['classes'][$cls])->name;
            if($code==1062){
                $msg="This employee is already assigned as a ".
                "teacher for Subject ( ".$sub
                ." ) in Class ( ".$class." ).";
            }else{
                $msg='Something went Wrong...error:'.$e->getMessage();
            }
            DB::rollBack();
            abort(400,$msg);
        }
        DB::commit();
     //Success!! Return response
        return Helper::success();
    }
    public function assignRoles(Employee $employee) {
        $data=request()->validate([
            'roles'=>['min:1','required','array']
        ]);
        Helper::assignRoles($employee,$data['roles']);
        return Helper::success();
    }
    public function removeRoles(Employee $employee) {
        $data=request()->validate([
            'roles'=>['min:1','required','array']
        ]);
        Helper::removeRoles($employee,$data['roles']);
        return Helper::success();
        //TODO: apply results of revoking the role
    }
}
