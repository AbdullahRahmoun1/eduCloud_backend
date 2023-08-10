<?php
namespace App\Tasks;

use App\Events\PrivateNotification;
use DateTime;
use DateInterval;
use App\Models\Student;
use App\Models\MoneyRequest as mr;
use App\Http\Controllers\MoneyRequestController;
use Exception;

class NotifyParentsToPayBills {
    public function __invoke() {
        $students=Student::all();
        $shouldBeWarned=[];
        $messagesSent=0;
        foreach($students as $student){
            $financeInfo=MoneyRequestController::getStudentsFinanceInformation($student,true);
            $bill=$financeInfo->schoolBill;
            if($bill)
            $this->checkBill(
                $bill,
                $student,
                $shouldBeWarned
            );
            $bill=$financeInfo->busBill;
            if($bill)
            $this->checkBill(
                $bill,
                $student,
                $shouldBeWarned
            );
            
        }
        //TODO insert notification to db
        return $shouldBeWarned;
        try{
            foreach($shouldBeWarned as $id=>$messages){
                foreach($messages as $message){
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
    public function checkBill($bill,$student,&$shouldBeWarned){
        $today = new DateTime();
        $sections=$bill->moneySubRequests;
                foreach($sections as $section){
                    $warningDate = new DateTime($section->final_date);
                    $interval = new DateInterval('P7D'); // P7D = period of 6 days
                    $warningDate = $warningDate->sub($interval);
                    //final date - 7 days we start warning them..
                    if(!$section->fully_paid && $today>=$warningDate){
                        $shouldBeWarned[$student->id][]=$this
                        ->constructMessage($student,$bill,$section);
                    }
                }
    }
    public function constructMessage($student,$bill,$section) {
        return [
            'title'=>$bill->type==mr::SCHOOL?"Delayed School payment.":"Delayed Bus payment.",
            'body'=>"A payment of $section->remaining S.P is required in "
            ."$student->full_name's account before $section->final_date."
        ];
    }
}