<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\PrivateNotification;
use DateTime;
use App\Models\MoneyRequest as mr;
use App\Http\Controllers\SchoolFinanceController;
use Exception;


class NotifyParentsToPayBills implements ShouldQueue,ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    
    public function __construct()
    {
        $this->queue="MyQueue";
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $messagesSent=0;
        $shouldBeWarned=SchoolFinanceController::getLateStudentsBills(with_warnings:true,send_response:false);
        try{
            //TODO insert notification to db
            foreach($shouldBeWarned as $id=>$bills){
                foreach($bills as $bill){
                    $message=self::constructMessage(
                        $bill['student'],
                        $bill['type'],
                        $bill['section']
                    );
                    event(
                        new PrivateNotification($id,$message,'finance')
                    );
                    $messagesSent++;
                }
                
            }
        }catch(Exception $e){
            return "Task notifyParentToPayBills failed , reason : {$e->getMessage()}";
        }
        return "Task notifyParentToPayBills succeeded!!"
        ." Students Notified : ".count($shouldBeWarned)
        ." Messages sent : ".$messagesSent;
    }
    public function constructMessage($student,$type,$section) {
        $today=new DateTime();
        $final_date=new DateTime($section->finalDate);
        if($today>$final_date){
            $diff=$today->diff($final_date)->days;
            $body=
            "Kindly proceed to the school for the payment of "
            ."$section->remaining S.P. Your payment is overdue by $diff days.";
        }else {
            $body=
            "A payment of $section->remaining S.P is required in "
            ."$student->full_name's account before $section->final_date.";
        }
        return [
            'title'=>$type==mr::SCHOOL?"Delayed School payment.":"Delayed Bus payment.",
            'body'=>$body
        ];
    }
}
