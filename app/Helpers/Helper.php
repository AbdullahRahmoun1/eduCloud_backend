<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as res;
use App\Models\ClassSupervisor;
use App\Models\ClassTeacherSubject;
use App\Models\GClass;
use App\Models\Student;
class Helper {
    public static function lazyQueryTry($toTry){
        try{
            $result=$toTry();
        }catch(QueryException $e){
            res::queryError($e);
        }
        return $result;
    }
    public static function success(){
        return [
            'message'=>'Success!!'
        ];
    }
    private static function checkRoles($roles,$emp,$shouldHave) {
        $msg=-1;
        foreach($roles as $role){
            if(config('roles.'.$role,-1)==-1){
                $msg="Role $role isn't an actual role..";
            }
            if($emp->hasRole($role)&&!$shouldHave){
                $msg="The employee already holds the role of a $role.";
            }
            if(!$emp->hasRole($role)&&$shouldHave){
                $msg="The employee does not have the role of a $role.";
            }
        }
        if($msg!=-1)
        abort(422,$msg);
    }
    public static function assignRoles($emp,$roles){
        Helper::checkRoles($roles,$emp,false);
        $account=$emp->account;
        foreach($roles as $role){
            $emp->assignRole($role);
        }
    }
    public static function removeRoles($emp,$roles){
        Helper::checkRoles($roles,$emp,true);
        $account=$emp->account;
        foreach($roles as $role){
            Helper::removeRoleDependencies($emp,$role);
            $emp->removeRole($role);
        }
    }

    public static function removeRoleDependencies($emp,$role){

        $id = $emp->id;
        $dependencies=null;
        if($role == config('roles.supervisor'))
            $dependencies = ClassSupervisor::where('employee_id',$id);
        else if($role == config('roles.teacher'))
            $dependencies = ClassTeacherSubject::where('employee_id', $id);
        //TODO: add bus dependencies when done
        if($dependencies!=null)
        $dependencies->delete();
    }

    public static function getEmployeeChannel($employee_id) {
        return "employee-$employee_id";
    }

    public static function getStudentChannel($student_id) {
        return "student-$student_id";
    }

    public static function onlyKeepAttributes(&$model,$wantedAttribs){
        $allAttribs=array_keys($model->getAttributes());
        $hiddenAttribs=array_diff($allAttribs,$wantedAttribs);
        $model->makeHidden($hiddenAttribs);
    }
    public static function tryToRead($class_id){
        if(Gate::denies('viewClassInfo',[GClass::class,$class_id]))
        res::error("You dont have the permission to read this class's data.",
        code:403);
    }
    
    public static function tryToEdit($class_id){
        if(Gate::denies('editClassInfo',[GClass::class,$class_id]))
        res::error("You dont have the permission to edit this class's data.",
        code:403);
    }

    public static function tryToReadStudent($student_id){
        if(Gate::denies('viewStudent',[Student::class,$student_id]))
        res::error("You dont have the permission to read this student's data.",
        code:403);
    }
}