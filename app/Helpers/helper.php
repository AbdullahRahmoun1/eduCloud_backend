<?php
namespace App\Helpers;

use Exception;
use App\Helpers\ResponseFormatter as res;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
            $emp->removeRole($role);
        }
    }

    public static function getEmployeeChannel($employee_id) {
        return "employee-$employee_id";
    }

    public static function getStudentChannel($student_id) {
        return "student-$student_id";
    }

}