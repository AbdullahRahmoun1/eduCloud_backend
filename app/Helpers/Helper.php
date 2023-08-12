<?php
namespace App\Helpers;

use App\Events\PrivateNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as res;
use App\Models\Category;
use App\Models\ClassSupervisor;
use App\Models\ClassTeacherSubject;
use App\Models\GClass;
use App\Models\Notification;
use App\Models\Student;
use Exception;
use Illuminate\Support\Str;
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
        res::error("You don't have the permission to read this class's data.",
        code:403);
    }
    
    public static function tryToEdit($class_id){
        if(Gate::denies('editClassInfo',[GClass::class,$class_id]))
        res::error("You don't have the permission to edit this class's data.",
        code:403);
    }

    public static function tryToReadStudent($student_id){
        if(Gate::denies('viewStudent',[Student::class,$student_id]))
        res::error("You don't have the permission to read this student's data.",
        code:403);
    }

    public static function sendNotificationToOneStudent($student_id, $body, $category_id, $notify = false, $sent = true){

        if(!Student::find($student_id))
            throw new Exception('invalid student id');
        
        if(!Category::find($category_id))
            throw new Exception('invalid category id');

        if(Str::length($body) > 300)
            throw new Exception('the body must be less that 300 chars');

        $data['owner_id'] = $student_id;
        $data['owner_type'] = Student::class;
        $data['body'] = $body;
        $data['category_id'] = $category_id;
        $data['date'] = now();

        $category = Category::find($category_id);

        //if the note is not sent or will not be sent -> put them as false
        if(((!$category->send_directly && !auth()->user()->owner->hasRole('principal')) || !$notify) && !$sent){
            $data['approved'] = false;
            $data['sent_successfully'] = false;
        }
        
        try{
            $notification = Notification::create($data);
        }
        catch(Exception $e){
            throw new Exception('something went wrong!',code:400);
        }

        if(($category->send_directly || auth()->user()->owner->hasRole('principal')) && $notify){
            event(new PrivateNotification(
                $student_id,
                ['title' => $category->name, 'body' => $body],
                $category->name));
        }

        return $notification;
    }
}