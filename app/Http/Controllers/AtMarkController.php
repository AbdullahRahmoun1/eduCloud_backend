<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter as res;
use App\Models\AbilityTest;
use App\Models\AtMark;
use App\Models\AtMarkSection;
use App\Models\AtSection;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtMarkController extends Controller
{
    public function add(){
     //Request Validation...
        $data=request()->validate([
            'student_id'=>['required','exists:students,id'],
            'ability_test_id'=>['required','exists:ability_tests,id'],
            'date'=>['required','date'],
            'sections_marks'=>['required','min:1','array'],
            'sections_marks.*.at_section_id'=>['required','numeric','exists:at_sections,id'],
            'sections_marks.*.mark'=>['required','numeric','min:0']
        ]);
        $student=Student::find($data['student_id']);
        $at=AbilityTest::find($data['ability_test_id']);
     //Logic Validation
      //is student's grade the same as ability test target grade?
        $studentsGrade=$student->g_class->grade;
        $abilityTestsGrade=$at->subject->grade;
        if($studentsGrade->id!=$abilityTestsGrade->id){
            res::error("This ability test is intended for grade ({$abilityTestsGrade->name})"
            .", while this student belongs to grade ({$studentsGrade->name}).");
        }
      //Are the sections provided correct?
       //bring helper data
        $at_sections_ids=$at->sections()->pluck('id');
        $req_sections_ids=array_map(
            fn($mark)=>$mark['at_section_id']
            ,$data['sections_marks']
        );
        $req_sections_at_Ids=AbilityTest::whereHas(
            'sections'
            ,fn($query)=>$query->whereIn('id',$req_sections_ids)
        )->pluck('id');
       //do they belong to one and only one at?
        if(count($req_sections_at_Ids)>1)
        res::error("The given at_sections belong to multiple ability tests"
        .", but they should belong to only one ability test.",code:422);
       //do they belong to the at provided in the request?
        if($req_sections_at_Ids[0]!=$data['ability_test_id'])
        res::error("The given \"at_sections\" doesn't belong to the given ability test",code:422);
       //does all at sections has a mark? == is there any section mark missing
        $diff=array_diff($at_sections_ids->toArray(),$req_sections_ids);
        if(count($diff)>0){
            res::error("Please include the missing sections and then attempt again.",code:422);
        }
       //is there any duplicate sectoins?     
        if(count(array_unique($req_sections_ids))
        !=count($req_sections_ids)){
            res::error("Duplicate section mark..fix it then try again.",code:422);
        }
       //does every mark matche the range of values for it's at_section?
        foreach($data['sections_marks'] as $mark){
            $sec=AtSection::find($mark['at_section_id']);
            if($mark['mark']>$sec->max_mark)
            res::error("\"{$sec->name}\" mark should be "
            ."less or equal to ( {$sec->max_mark} )"
            ,code:422);
        }
     //EVERTHING IS GOOD!!.Now create the data
        DB::beginTransaction();
        try{
            $result=[];
            $at_mark=AtMark::create(
                array_diff_key($data,['sections_marks'=>''])
            );
            foreach($data['sections_marks'] as $mark){
                $mark['at_mark_id']=$at_mark->id;
                $result[]=AtMarkSection::create($mark);
            }
        }catch(QueryException $e){
            res::queryError($e,rollback:true);
        }
        DB::commit();
        res::success(data:[
            'at_mark'=>$at_mark,
            'at_markSections'=>$result
        ]);
    }
}
