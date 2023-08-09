<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\Income;
use App\Models\MoneyRequest as mr;
use App\Models\MoneyRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function add(Student $student) {
        $data=request()->validate([
            'value'=>['required','numeric','min:1000'],
            'forSchoolBill?'=>['required','boolean'],
            'notes'=>['string','max:60'],
        ]);
        $data['student_id']=$student->id;
        $data['date']=now();
        $data['type']=
        $data['forSchoolBill?']?mr::SCHOOL:mr::BUS;
        unset($data['forSchoolBill?']);
        //prepare data
        $bill=$student->moneyRequests()
        ->where('type',$data['type'])
        ->first();
        $payments=Income::where('student_id',$student->id)
        ->where('type',$data['type'])
        ->sum('value');
        //check if he has any bills 
        if($bill==null){
            res::error(
                "This student doesn't have any bill of type ( {$data['type']} )."
                ,code:422
            );  
        }
        //check if already completely paid the bill 
        //or the amount left is smaller than provided amount.
        if($bill->value<$payments+$data['value']){
            $left=$bill->value-$payments;
            //TODO: remove this line .. (just because seeding data is dump)
            $left=$left<0?0:$left;
            //-----------
            if($left==0)
            res::error(
                "This bill has already been completely paid.",
                code:422
            );
            else
            res::error(
                "The bill currently has a remaining balance of $left "
                ."that needs to be paid, but the amount"
                ." you provided is {$data['value']} .",
                code:422
            );
        }
        //looks good.. insert to db
        $income=Helper::lazyQueryTry(
            fn()=>Income::create($data)
        );
        res::success(data:$income);
    }
    public function get(Student $student){
        // incomes come already sorted in ascending order
        // (ascending) because it helps in another 
        //route and can be fixed easily in this route
        $data=request()->validate([
            'schoolPayments?'=>['boolean']
        ]);
        if(isset($data['schoolPayments?'])){
            $incomes=$student->incomes()
            ->whereType(
                MoneyRequest::getAppropriateType($data['schoolPayments?'])
            )->get()->reverse();
        }else
        $incomes=$student->incomes->reverse();
        res::success(data:$incomes);
    }
}
