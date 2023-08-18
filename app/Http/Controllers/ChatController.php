<?php

namespace App\Http\Controllers;

use App\Events\Complaint as EventsComplaint;
use App\Events\Reply as EventsReply;
use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\GClass;
use App\Models\Grade;
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
                    "Failed..You don't have the permission to chat with this student.",
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
            event(new EventsReply($student->id,$rep));
        }
        res::success();
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
                $role='other';
                if($r->employee->hasRole(config('roles.supervisor')))
                $role=config('roles.supervisor');
                if($r->employee->hasRole(config('roles.principal')))
                $role=config('roles.principal');
                $r->employee->makeHidden('roles');
                $r->employee->role=$role;   
            }
        }
        res::success(data:$result->values());
    }
    public function getSupervisorsConversations() {
        //FIXME find a better way this will be slow in future
        $owner=request()->user()->owner;
        if($owner->hasRole(config('roles.principal'))){
            $allowedToViewStudents = Student::all()->pluck('id');
        }else if($owner->hasRole(config('roles.supervisor'))){
            $classes=$owner->g_classes_sup;
            $allowedToViewStudents=[];
            foreach($classes as $class){
                $allowedToViewStudents+=
                $class->students->pluck('id')->toArray();
            }
        }else {
            res::error("You have to be a principal or supervisor to call this route!!");
        }
        $complaints=Complaint::whereIn('student_id',$allowedToViewStudents)->get();;
        $replies=Reply::whereIn('student_id',$allowedToViewStudents)->get();;
        $messages=$complaints->merge($replies);
        $messages=$messages->sortByDesc('date_time');
        return $messages;
        // $messages->sortBy
        $result=collect();
        foreach($messages as $message){
            if($result->has($message->student_id))
            continue;
            $message->load([
                'student:id,first_name,last_name,grade_id,g_class_id',
                'student.grade:id,name',
                'student.g_class:id,name'
            ]);
            $message['complaint?']=$message instanceof Reply;
            $result[$message->student_id]=$message;
        }
        return $result->values();

        //TODO: add this filter in future!!
        // $data=request()->validate([
        //     'grade_id'=>['exists:grades,id'],
        //     'class_id'=>'exists:g_classes,id',
        // ]);
        // $owner=request()->user()->owner;
        // if($owner->hasRole(config('roles.principal'))){
        //     $allowedGrades = Grade::all()->pluck('id');
        //     $allowedClasses = GClass::all()->pluck('id');
        // }else if($owner->hasRole(config('roles.supervisor'))){
        //     $classes=$owner->g_classes_sup;
        //     $allowedClasses=$classes->pluck('id');
        //     $allowedGrades = $classes->pluck('grade_id')->unique();
        // }else {
        //     res::error("You can't call this route!!");
        // }
        // if(isset($data['grade_id']) && !$allowedGrades->contains('grade_id')){   
        //     res::error(
        //         "You don't have the access to view "
        //         ."this grade student conversations."
        //     );
        // }
        // if(isset($data['grade_id']) && !$allowedGrades->contains('grade_id')){   
        //     res::error(
        //         "You don't have the access to view "
        //         ."this grade student conversations."
        //     );
        // }
    }
}
