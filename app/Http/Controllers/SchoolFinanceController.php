<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use DateTime;
use DateInterval;
use App\Models\Income;
use App\Models\Student;
use App\Models\MoneyRequest as mr;

class SchoolFinanceController extends Controller
{
    public function generalStudy() {
        $result=collect();
        $result['schoolBills']=mr::where('type',mr::SCHOOL)->sum('value');
        $result['busBills']=mr::where('type',mr::BUS)->sum('value');
        $result['schoolPayments']=Income::where('type',mr::SCHOOL)->sum('value');
        $result['busPayments']=Income::where('type',mr::BUS)->sum('value');
        $lateStudents=collect(self::getLateStudentsBills(send_response:false));
        $result['studentsWithLateBills']=count($lateStudents);
        $busSumOfLateBills=0;
        $schoolSumOfLateBills=0;
        foreach($lateStudents as $bills){
            foreach($bills as $bill){
                if($bill['type']==mr::SCHOOL)
                $schoolSumOfLateBills+=$bill['section']['remaining'];
                else 
                $busSumOfLateBills+=$bill['section']['remaining'];
            }
        }
        $result['balanceOfSchoolLateBills']=$schoolSumOfLateBills;
        $result['balanceOfBusLateBills']=$busSumOfLateBills;
        return $result;
    }

    public function specificMonthStudy() {
        //TODO: implement in future
    }

    public static function getLateStudentsBills($with_warnings=false,$send_response=true) {
        $with_warnings=request('with_warnings')??$with_warnings;
        //get student that match the search query
        $students=Student::when(
            request('grade_id')??false,
            fn($q)=>$q->where('grade_id',request('grade_id'))
        )->when(
            request('class_id')??false,
            fn($q)=>$q->where('g_class_id',request('class_id'))
        )
        ->get();
        //load class and grade information
        $students->load(['grade:id,name','g_class:id,name']);        
        $shouldBeWarned=[];
        foreach($students as $student){
            //get student's financial information
            $financeInfo=MoneyRequestController::getStudentsFinanceInformation($student->id,true);
            $bill=$financeInfo->schoolBill;
            //if he has a school bill then check it
            if($bill)
            self::checkBill(
                $bill,
                $student,
                $shouldBeWarned,
                $with_warnings?7:0
            );
            $bill=$financeInfo->busBill;
            //if he has a bus bill then check it
            if($bill)
            self::checkBill(
                $bill,
                $student,
                $shouldBeWarned,
                $with_warnings?7:0
            );
        }
        if(!$send_response)
        return $shouldBeWarned;
        else{
            //prepare the output
            $lateStudents=[];
            foreach($shouldBeWarned as $lateBills){
                //get student's data
                $student=$lateBills[0]['student'];
                //remove unnecessary data
                Helper::onlyKeepAttributes($student,[
                    'id','first_name','last_name',
                    'full_name'
                ]);
                //get all student's bills and assign them to him
                $student->lateBills=array_map(function($bill){
                    $bill['section']['type']=$bill['type'];
                    return $bill['section'];
                },$lateBills);
                //add student to result
                $lateStudents[]=$student;
            }
            res::success(data:$lateStudents);
        }
    }

    private static function checkBill($bill,$student,&$shouldBeWarned,$dayInterval=7){
        $today = new DateTime();
        $bill=$bill->moneySubRequests;
                foreach($bill as $bill){
                    $final_date = new DateTime($bill->final_date);
                    $interval = new DateInterval("P{$dayInterval}D"); // P7D = period of 6 days
                    $final_date = $final_date->sub($interval);
                    //final date - 7 days we start warning them..
                    if(!$bill->fully_paid && $today>=$final_date){
                        $shouldBeWarned[$student->id][]=[
                            'student'=>$student,
                            'type'=>$bill->type,
                            'section'=>$bill,
                        ];
                    }
                }
    }
}
