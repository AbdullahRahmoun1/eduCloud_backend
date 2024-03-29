<?php

namespace App\Http\Controllers;

use App\Events\PrivateNotification;
use App\Helpers\Helper;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\Category;
use App\Models\Student;
use App\Models\Employee;
use DateTime;
use Dotenv\Parser\Entry;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function global(Request $request) {
        
        $data = $request->validate([
            'body' => ['required', 'min:1', 'max:300'],
            'title' => ['required', 'min:1', 'max:30']
        ]);
        $data['date'] = now();
        $data['category_id'] = 1;
        $data['owner_id'] = 0;
        $data['owner_type'] = Student::class;

        $notification = Notification::create($data);
        $notification->makeHidden('owner_id', 'owner_type');

        //TODO: add event
        res::success('notification sent to all students successfully', $notification);
    }
    
    public function getNotificationsOfStudent($student_id){

        if($student_id < 0 ){
            if(auth()->user()->owner_type == Employee::class){
                res::error('invalid student id',code:422);
            }
            $student_id = auth()->user()->owner->id;
        }

        $student = Student::find($student_id);
        if(!$student){
            res::error('invalid student id',code:422);
        }

        Helper::tryToReadStudent($student->id);

        $query = Notification::query()
        ->with('category:id,name')
        ->where(function($q) use ($student){
            $q->where('owner_id', $student->id)
            ->orWhere('owner_id', 0);
        })
        ->where('owner_type', Student::class)
        ->orderBy('date','desc')
        ->select('id', 'owner_id', 'category_id','title', 'body', 'date','sent_successfully', 'approved');

        if(auth()->user()->owner_type == Student::class){
            $query->where('sent_successfully',true);
        }

        if(request()->has('page')){
            $notifications = $query->simplePaginate(10);
        }
        else{
            $notifications = $query->get();
        }

        res::success(data:$notifications);
    }
    
    public function sendNotificationsToStudents($notify){

        $data = request()->validate([
            '*' => ['required', 'array'],
            '*.owner_id' => ['required', 'exists:students,id'],
            '*.title' => ['required', 'min:1', 'max:300'],
            '*.body' => ['required', 'min:1','max:30'],
            '*.category_id' => ['required', 'exists:categories,id']
        ]);

        $entryNum = 1;
        $events = [];
        $notifications = [];

        DB::beginTransaction();
        try{

            foreach($data as $entry){

                //make sure this employee is allowed to notify students
                if(Gate::denies('viewStudent',[Student::class,$entry['owner_id']])){
                    throw new Exception("You don't have the permission to send notification this student.");
                }
                
                $category = Category::find($entry['category_id']);

                $will_be_sent = (auth()->user()->owner->hasRole('principal') ||
                $category->send_directly) && $notify;

                $notifications[] = Helper::sendNotificationToOneStudent(
                    $entry['owner_id'],
                    $entry['title'],
                    $entry['body'],
                    $entry['category_id'],
                    sent:$will_be_sent
                );

                if($will_be_sent){

                    $events[] = new PrivateNotification(
                        $entry['owner_id'],
                        ['title' => $entry['title'], 'body' => $entry['body']],
                        $category->name);
                }
                $entryNum++;
            }
        }
        catch(Exception $e){
            $message = $e->getMessage();
            res::error("error in entry $entryNum ... $message", rollback:true);
        }

        foreach($events as $event)
            event($event);

        res::success('notifications sent successfully', $notifications, true);
    }

    public function getUnsentNotifications(){

        $query = Notification::query()
        ->where('sent_successfully',false)
        ->where('owner_type', Student::class);

        if(request()->has('category_id')){
            $query->where('category_id', request()->category_id);
        }
        
        if(request()->has('owner_id')){
            $query->where('owner_id', request()->owner_id);
        }
        
        if(request()->approved == 1){
            $query->where('approved', 1);
        }
        
        if(request()->has('approved') && request()->approved == 0){
            $query->where('approved', 0);
        }
        
        //TODO: fix supervisor authorization
        
        // $employee = auth()->user()->owner;
        // if($employee->hasRole('supervisor')
        // &&
        // !$employee->hasRole('secretary') &&
        // !$employee->hasRole('principal')
        // ){

        //     $his_classes = $employee->g_classes_sup->pluck('g_class_id');
        //     return $notifications = Notification::whereHas('owner', function ($query) {
        //         $query->where('g_class_id', 5);
        //     }, Student::class)->get();
        //     $query->whereHas('owner',fn($query) =>
        //         $query->whereIn('g_class_id',$his_classes)
        //     ,Student::class);
        // }
        
        $query->orderBy('date', 'desc');

        if(request()->has('page')){
            $notifications = $query->simplePaginate(10);
        }
        else{
            $notifications = $query->get();
        }
        

        res::success(data:$notifications);
    }

    public function approveNotifications(){

        $data = request()->validate([
            'notifications' => ['required', 'array'],
            'notifications.*' => ['exists:notifications,id']
        ]);

        DB::beginTransaction();

        $entryNum = 1;

        foreach($data['notifications'] as $entry){
            
            $noti = Notification::find($entry);
            $wannaSend = request()->send == 1;
            if($wannaSend && $noti['sent_successfully']){
                res::error("error in entry number $entryNum .... this notification is already sent");
            }
            if(!$wannaSend && $noti['approved'] == 1){
                res::error("error in entry number $entryNum .... this notification is already approved");
            }

            $entryNum++;
        }
    }
}
