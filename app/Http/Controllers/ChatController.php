<?php

namespace App\Http\Controllers;

use App\Events\Complaint as EventsComplaint;
use App\Events\Reply as EventsReply;
use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\GClass;
use App\Models\Reply;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ChatController extends Controller
{
    public function post($student_id) {
        if($student_id<=0){
            $student=request()->user()->owner;
            if(!($student instanceof Student))
            res::error("Failed..You'r an employee..you have to select a student to reply to.",code:422);
        }else{
            $student=Student::find($student_id);
            if($student==null){
                res::error("Student not found.",code:404);
            }
        }
        $data=request()->validate([
            'message'=>['required','string','min:1'],
        ]);
        $user=request()->user();
        $type=$user->owner_type;
        $owner=$user->owner;
        if($type==Student::class){
            if($student->id!=$owner->id)
            res::error(
                "Failed..You dont have the permission to chat with this student.",
                code:403
            );
            $comp=Helper::lazyQueryTry(
                fn()=>Complaint::create([
                    'body'=>$data['message'],
                    'date_time'=>now(),
                    'student_id'=>$student->id
                ])    
            );
            event(new EventsComplaint($student->id,$comp));
        }else{
            try{
                $this->authorize('editClassInfo',[GClass::class,$student->g_class_id]);
            }catch(Exception $e){
                res::error(
                    "Failed..You dont have the permission to chat with this student.",
                    code:403
                );
            }
            $rep=Helper::lazyQueryTry(
                fn()=>Reply::create([
                    'body'=>$data['message'],
                    'date_time'=>now(),
                    'student_id'=>$student->id,
                    'employee_id'=>$owner->id
                ])
            );
            event(new EventsReply($owner->id,$student->id,$rep));
        }
        return $data;
    }
    public function getChat($student_id) {
        $owner=request()->user()->owner;
        if($student_id<=0){
            $student=$owner;
            if(!($student instanceof Student))
            res::error("Failed..You'r an employee..you have to ".
            "select a student to get you'r chat with him.",code:422);
        }else{
            $student=Student::find($student_id);
            if($student==null){
                res::error("Student not found.",code:404);
            }
        }
        $isEmployee=$owner instanceof Employee;
        $comp=$student->complaints;
        $reps=$student->replies;
        if($isEmployee)
        $reps->load(['employee']);
        $result=$comp->merge($reps);
        $result=$result->sortBy('id');
        $result=$result->sortBy('date_time');
        foreach ($result as $r) {
            $r->makeHidden([
                'id','student_id','employee_id',
                'created_at','updated_at']);
            $r->complaint=$r instanceof Complaint;
            if($isEmployee && !$r->complaint){
                $r->fromYou=$owner->id==$r->employee_id;
                if($r->employee->hasRole(config('roles.supervisor')))
                $role=config('roles.supervisor');
                if($r->employee->hasRole(config('roles.principal')))
                $role=config('roles.principal');
                $r->employee->makeHidden('roles');
                $r->employee->role=$role;   
            }
        }
        res::success(data:$result);
    }
}
