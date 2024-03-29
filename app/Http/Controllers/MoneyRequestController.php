<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use DateInterval;
use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\MoneySubRequest;
use App\Models\MoneyRequest as mr;
use Illuminate\Support\Facades\DB;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as res;

class MoneyRequestController extends Controller
{
    public function add(Student $student) {
        $data=request()->validate([
            'totalValue'=>['required','min:1000','numeric'],
            'notes'=>['string','max:70'],
            'schoolBill?'=>['required','boolean'],
            'bill_sections'=>['required','array','min:1'],
            'bill_sections.*.value'=>['required','numeric','between:1000,'.request()->totalValue],
            'bill_sections.*.final_date'=>['required','date','after:'.now(),'distinct']
        ]);
        //check if bills section match the total value
        $sSum=0;
        foreach($data['bill_sections'] as $billS)
        $sSum+=$billS['value'];
        if($sSum!=$data['totalValue'])
        res::error(
            "Bill sections must be equal to the actual Bill value.. "
            ." Bill sections sum : $sSum , Total value : ".$data['totalValue'].'.',
            code:422
        );
        //create them
        try{
            DB::beginTransaction();
            //create money request
            $money_request=mr::create([
                'value'=>$data['totalValue'],
                'type'=>mr::getAppropriateType($data['schoolBill?']),
                'notes'=>$data['notes']??null,
                'student_id'=>$student->id
            ]);
            //now the sections
            foreach($data['bill_sections'] as $section){
                $section['money_request_id']=$money_request->id;
                MoneySubRequest::create($section);
            }
        }catch(QueryException $e){
            $type=$data['schoolBill?']?"School bill":"Bus bill";
            $dupMsg="This student already has a bill of type ( $type )";
            res::queryError($e,$dupMsg,rollback:true);
        }
        $money_request->load(['moneySubRequests']);
        res::success(data:$money_request,commit:true);
    }
    public function edit(mr $bill) {
        $data=request()->validate([
            'totalValue'=>['required_with:totalValue','min:1000','numeric'],
            'notes'=>['string','max:70'],
            'bill_sections'=>['required_with:totalValue','array','min:1'],
            'bill_sections.*.value'=>['numeric','between:1000,'.request()->totalValue],
            'bill_sections.*.final_date'=>['date','after:'.now(),'distinct']
        ]);
        //check if bills section match the total value
        $sSum=0;
        $totalValue=$data['totalValue'];
        foreach($data['bill_sections'] as $billS)
        $sSum+=$billS['value'];
        if($sSum!=$totalValue)
        res::error(
            "Bill sections must be equal to the actual Bill value.. "
            ." Bill sections sum : $sSum , Total value : ".$data['totalValue'].'.',
            code:422
        );   
        $money_request=Helper::lazyQueryTry(function () use ($data,$bill,$totalValue){
            //nice.. edit the bill 
            $notes=$data['notes']??$bill->notes;
            $bill->value=$totalValue;
            $bill->notes=$notes;
            $bill->save();
            //delete the previous sections
            $bill->moneySubRequests()->delete();
            //now the sections
            foreach($data['bill_sections'] as $section){
                $section['money_request_id']=$bill->id;
                MoneySubRequest::create($section);
            }
            return $bill;
        });
        $money_request->load(['moneySubRequests']);
        res::success(data:$money_request);
    }
    public static function getStudentsFinanceInformation($student_id,$onlyGetData=false){
        if($student_id==-1){
            $owner=request()->user()->owner;
            if(!$owner->hasRole(config('roles.student')))
            res::error(
                "You can't get your financial information. you'r not a student.",
                code:422
            );
            $student_id=$owner->id;
        }
        $student=Student::find($student_id);
        $student->load([
            'moneyRequests','moneyRequests.moneySubRequests',
            'incomes'
        ]);
        //note: incomes come already sorted in ascending order
        if(!$onlyGetData)
        Helper::tryToReadStudent($student->id);
        //get how much he paid
        $paidForSchool=$student->incomes
        ->where('type',mr::SCHOOL)
        ->sum('value');
        $paidForBus=$student->incomes
        ->where('type',mr::BUS)
        ->sum('value');
        //assign to student model
        $student->paidForSchoolBill=$paidForSchool;
        $student->paidForBusBill=$paidForBus;
        //Now assign to every sub request if it is fully paid or what is left
        foreach($student->moneyRequests as $request){
            $type=$request->type==mr::SCHOOL
            ?'paidForSchool':'paidForBus';
            //note: sub requests come already sorted in ascending order!
            foreach($request->moneySubRequests as $subRequest){
                if($subRequest->value <= $$type){
                    $subRequest->fully_paid=true;
                    $subRequest->remaining=0;
                    $$type-=$subRequest->value;
                }else{
                    $subRequest->fully_paid=false;
                    $remaining=$subRequest->value-$$type;
                    $subRequest->remaining=$remaining;
                    $$type=0;
                }
            }
        }
        //set the busBill and schoolBill
        $student->schoolBill=$student->moneyRequests
        ->where('type',mr::SCHOOL)->first();
        $student->busBill=$student->moneyRequests
        ->where('type',mr::BUS)->first();
        //remove unnecessary data
        unset($student->incomes);
        unset($student->moneyRequests);
        Helper::onlyKeepAttributes($student,[
            'paidForBusBill','paidForSchoolBill',
            'schoolBill','busBill'
        ]);
        $student->hideFullName();
        //now return the response
        if($onlyGetData)
        return $student;
        else
        res::success(data:$student);
    }
}
