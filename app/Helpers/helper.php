<?php
namespace App\Helpers;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Helper {
    public static function lazyQueryTry($toTry){
        DB::beginTransaction();
        try{
            $toTry();
        }catch(QueryException $e){
            $code=$e->errorInfo[1];
            if($code==1062){
                $msg="Duplicate error..ErrorInfo:{$e->errorInfo[2]}";
            }else{
                $msg='Something went Wrong...error:'.$e->getMessage();
            }
            DB::rollBack();
            abort(400,$msg);
        }
        DB::commit();
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

}