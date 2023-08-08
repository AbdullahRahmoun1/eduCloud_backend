<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\MoneyRequest;
use App\Models\MoneySubRequest;
use App\Models\Student;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        res::error("Bill sections must be equal to the actual Bill value.. "
        ." Bill sections sum : $sSum , Total value : ".$data['totalValue'].'.');
        //create them
        try{
            DB::beginTransaction();
            //create money request
            $money_request=MoneyRequest::create([
                'value'=>$data['totalValue'],
                'type'=>MoneyRequest::getAppropriateType($data['schoolBill?']),
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
    public function edit(MoneyRequest $bill) {
        $data=request()->validate([
            'totalValue'=>['min:1000','numeric'],
            'notes'=>['string','max:70'],
            'bill_sections'=>['array','min:1'],
            'bill_sections.*.value'=>['numeric','between:1000,'.request()->totalValue],
            'bill_sections.*.final_date'=>['date','after:'.now(),'distinct']
        ]);
        //check if bills section match the total value
        $sSum=0;
        $totalValue=$data['totalValue']??$bill->value;
        foreach($data['bill_sections'] as $billS)
        $sSum+=$billS['value'];
        if($sSum!=$totalValue)
        res::error("Bill sections must be equal to the actual Bill value.. "
        ." Bill sections sum : $sSum , Total value : $totalValue .");
        
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
}
