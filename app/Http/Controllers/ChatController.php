<?php

namespace App\Http\Controllers;

use App\Events\Complaint as EventsComplaint;
use App\Events\Reply as EventsReply;
use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\Complaint;
use App\Models\GClass;
use App\Models\Reply;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ChatController extends Controller
{
    public function post(Student $student) {
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
                    'sutdent_id'=>$student->id
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
}
