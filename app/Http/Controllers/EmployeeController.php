<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\GClass;
use App\Helpers\Helper;
use App\Models\Account;
use App\Models\Subject;
use App\Models\Employee;
use PHPUnit\TextUI\Help;
use App\Models\ClassSupervisor;
use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter as res;
use Illuminate\Support\Facades\DB;
use App\Models\ClassTeacherSubject;
use App\Models\Student;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Database\QueryException;
use LDAP\Result;

class EmployeeController extends Controller
{   
    public function add(){
        //Validation
        $f=['required','string','between:2,25'];
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
                return res::error("Role $role isn't an actual role..",code:422);
     //create employee & account                
        DB::beginTransaction();
        try{
            //create the employee
            $emp=Employee::create($data);
            //assign the roles to him
            array_map(fn($role)=>$emp->assignRole($role)
            ,$data['roles']);
            //now create an account for him :)  
            $acc=Account::createAccount($emp, 1);
        }catch(QueryException $e){
            res::queryError($e,rollback:true);
        }
        DB::commit();
        //.................
        res::success('Employee was added successfully',[
            'account info'=>$acc,
            'employee' => $emp
        ]);
    }
    public function edit(Employee $employee){
     //Validation
        $l=Rule::unique('employees','last_name')
        ->where('first_name',request('first_name'));
        $data=request()->validate([
            'first_name'=>['required','string','between:2,25'],
            'last_name'=>['required_with:first_name','string','between:2,25',$l],
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
        ]);
     //Is he a supervisor?
        if(!$sup->hasRole(config('roles.supervisor')))
        res::error('This employee isn\'t a supervisor',code:422);
     //Assign Classes to him in the DB
        $ctr=0;
        DB::beginTransaction();
        $old_data = ClassSupervisor::where('employee_id',$sup->id);
        $old_data->delete();
        try{
            foreach($data['classes'] as $class){
                ClassSupervisor::create([
                    'g_class_id'=>$class,
                    'employee_id'=>$sup->id
                ]);
                $ctr++;
            }
        }catch(QueryException $e){
            $dupMsg="The employee is already assigned as ".
            "a supervisor for the class with ID : {$data['classes'][$ctr]}";
            res::queryError($e,$dupMsg,rollback:true);
        }
     //Success!!, return message   
        DB::commit();
        res::success();
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
        res::error('wth?..Empty body',code:422);
     //Is he a teacher?
        if(!$teacher->hasRole(config('roles.teacher')))
        res::error('This employee isn\'t a teacher',code:422);
     //Does he teach the same grade as the subjects belongs to
        foreach($data as $item){
            $subject=Subject::find($item['subject_id']);
            $classes=GClass::whereIn('id',$item['classes'])
            ->get();
            foreach($classes as $class){
                if($class->grade_id!=$subject->grade_id){
                    res::error("Class $class->name doesn't take subject $subject->name."
                    ." They belong to different grades.");
                }
            }
        }
     //Assign values to DB
        DB::beginTransaction();
        $it=0;
        $cls=0;
        $old_data = ClassTeacherSubject::where('employee_id',$teacher->id);
        $old_data->delete();
        try{
            foreach($data as $item){
                foreach($item['classes'] as $class){
                    ClassTeacherSubject::create([
                        'employee_id'=>$teacher->id,
                        'subject_id'=>$item['subject_id'],
                        'g_class_id'=>$class
                    ]);
                    $cls++;
                }
                $cls=0;
                $it++;
            }
        }catch(QueryException $e){
     //Error :( , return response
            $sub=Subject::find($data[$it]['subject_id'])->name;
            $class =GClass::find($data[$it]['classes'][$cls])->name;
            $dupMsg = "This employee is already assigned as a ".
            "teacher for Subject ( ".$sub
            ." ) in Class ( ".$class." ).";
            res::queryError($e,$dupMsg,rollback:true);
        }
        DB::commit();
     //Success!! Return response
        res::success();
    }
    public function employeesWithRole($role){
        if(config("roles.$role",-1)==-1)
        res::error("This role isn't an actual role!",code:422);
        $emps=Employee::whereHas("roles", 
        fn($q)=>$q->where("name", $role))
        ->get();
        res::success(data:$emps);
    }

    public function search($search){

        $role=request('role');
        if(isset($role) && config("roles.$role",-1)==-1)
        res::error("This role isn't an actual role!",code:422);

        $result = Employee::query()
        ->where(DB::raw('CONCAT(first_name," ",last_name)'),'like',"%$search%");

        if(isset($role)){
            $result->whereHas('roles', function($query) use ($role){
                $query->where('name',$role);
            });
        }

        $result = $result->with('roles:id,name')
        ->orderByDesc('id')->simplePaginate(10);

        res::success('results found successfully.', $result);
    }
    
    public function viewEmployee($employee){
        if($employee<=0){
            $employee=request()->user()->owner;
            if($employee instanceof Student){
                res::error(
                    "You can't call this route. you don't have the right permissions.",
                    code:403
                );
            }
            $employee=$employee->id;
        }
        $employee=Employee::findOrFail($employee);
        Helper::tryToReadEmployee($employee->id);
     //Roles....  
        $roles=$employee->getRoleNames()->toArray();
        $employee->makeHidden('roles');
        $currentRoles=[];
        foreach($roles as $role){
            $currentRoles[$role]=[];
        }
     //Supervisor data...
        $role=config('roles.supervisor');
        if($employee->hasRole($role)){
            //get the classes he is sup on them
            $employee->load(['g_classes_sup:id,name,grade_id','g_classes_sup.grade']);
            $employee->makeHidden('g_classes_sup');
            //fix data representation
            $ofClasses=$employee->g_classes_sup->map(function($class){
                $class->grade_name=$class->grade->name;
                $class->makeHidden('grade');
                return $class;
            });
            //set the new data to "ofClasses" field
            $currentRoles[$role]['ofClasses']=$ofClasses;
        }
     //Teacher data
        $role=config('roles.teacher');
        if($employee->hasRole($role)){
            $aha=ClassTeacherSubject::joins()
            ->join('grades AS g','g.id','=','c.grade_id')
            ->select('s.name AS subject_name',
            's.id AS subject_id',
            'g.name AS grade_name',
            'g.id AS grade_id',
            'c.name AS class_name',
            'c.id AS class_id',)
            ->where('e.id',$employee->id)
            ->get();
            $currentRoles[$role]['teaches']=$aha;
        }
     //Bus Admin data...
        $role=config('roles.busSupervisor');
        if($employee->hasRole($role)){
            $employee->load('buses');
            $employee->makeHidden('buses');
            $buses=$employee->buses;
            $currentRoles[$role]['supervisesBuses']=$buses;
        }
     //Response
        $employee->cuurentRoles=$currentRoles;
        return $employee;
        
    }

    public function regeneratePassword(Employee $emp){

        $account = $emp->account;
        if(!isset($account)){
            return res::error('this employee does not have an account',null,422);
        }

        try{
            $new_password = Account::changePassword($account);
        }
        catch(Exception $e){
            return res::error('something went wrong', $e->getMessage());
        }

        return res::success('password changed successfully.',[
            'account' => $account['user_name'],
            'new password' => $new_password
        ]);
    }
}
