<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\MoneyRequest as mr;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class SchoolFinanceController extends Controller
{
    public function generalStudy() {
        $result=collect();
        $result['schoolBills']=mr::where('type',mr::SCHOOL)->sum('value');
        $result['busBills']=mr::where('type',mr::BUS)->sum('value');
        $result['schoolPayments']=Income::where('type',mr::SCHOOL)->sum('value');
        $result['busPayments']=Income::where('type',mr::BUS)->sum('value');
        $lateStudents=collect(MoneyRequestController::getLateStudentsBills());
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
}
