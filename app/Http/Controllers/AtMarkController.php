<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter as res;
use App\Models\AbilityTest;
use App\Models\AtMark;
use App\Models\AtMarkSection;
use App\Models\AtSection;
use App\Models\CandidateStudent;
use App\Models\Student;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AtMarkController extends Controller
{
    public function add(){
        //Request Validation...
        $data=request()->validate([
            '*.student_id'=>['required'],
            '*.is_candidate' => ['required', 'boolean'],
            '*.ability_test_id'=>['required','exists:ability_tests,id'],
            '*.date'=>['required','date'],
            '*.sections_marks'=>['required','min:1','array'],
            '*.sections_marks.*.at_section_id'=>['required','numeric','exists:at_sections,id'],
            '*.sections_marks.*.mark'=>['required','numeric','min:0'],
            '*.is_entry_mark'=>['required','boolean']
        ]);

        $finalResult = [];
        $entryNum = 0;
        DB::beginTransaction();
        foreach($data as $entry){
            $is_cand = $entry['is_candidate'];
            $student = $is_cand ?
                CandidateStudent::find($entry['student_id']) :
                Student::find($entry['student_id']);
            $at=AbilityTest::find($entry['ability_test_id']);
        //Logic Validation
        //is this student id valid?
            if(!isset($student)){
                DB::rollBack();
                res::error("in entry number: $entryNum ... this student id is invalid");
            }
        //is student's grade the same as ability test target grade?
            $studentsGrade = $is_cand ? $student->grade :
                $student->g_class->grade;
            $abilityTestsGrade=$at->subject->grade;
            if($studentsGrade->id!=$abilityTestsGrade->id){
                DB::rollBack();
                res::error("in entry number: $entryNum ... This ability test is intended for grade ({$abilityTestsGrade->name})"
                .", while this student belongs to grade ({$studentsGrade->name}).");
            }
        //if he is a candidate then the mark should be an entry mark
        if($is_cand&&!$entry['is_entry_mark']){
            res::error("Candidate student {$student->full_name()} can only have entry test marks.",code:422,rollback:true);
        }
        //if he is a direct then the mark shouldn't be an entry mark
        if(!$is_cand&&$entry['is_entry_mark']){
            res::error("Direct student {$student->full_name()} can't  have entry test marks.",code:422,rollback:true);
        }
        //if this is an entry mark..does he have another entry mark?
        if($entry['is_entry_mark']){
            $count=AtMark::where('student_id',$student->id)
            ->where('student_type',$is_cand?CandidateStudent::class:Student::class)
            ->where('is_entry_mark',false)
            ->count();
            if($count!=0){
                res::error("Student {$student->full_name()} already has an entry mark.",
                code:422,rollback:true);
            }
        }
        //Are the sections provided correct?
        //bring helper data
            $at_sections_ids=$at->sections()->pluck('id');
            $req_sections_ids=array_map(
                fn($mark)=>$mark['at_section_id']
                ,$entry['sections_marks']
            );
            $req_sections_at_Ids=AbilityTest::whereHas(
                'sections'
                ,fn($query)=>$query->whereIn('id',$req_sections_ids)
            )->pluck('id');
        //do they belong to one and only one at?
            if(count($req_sections_at_Ids)>1){
                DB::rollBack();
                res::error("in entry number: $entryNum ... The given at_sections belong to multiple ability tests"
                .", but they should belong to only one ability test.",code:422);
            }
        //do they belong to the at provided in the request?
            if($req_sections_at_Ids[0]!=$entry['ability_test_id']){
                DB::rollBack();
                res::error("in entry number: $entryNum ... The given \"at_sections\" doesn't belong to the given ability test",code:422);
            }
            //does all at sections has a mark? == is there any section mark missing
            $diff=array_diff($at_sections_ids->toArray(),$req_sections_ids);
            if(count($diff)>0){
                DB::rollBack();
                res::error("in entry number: $entryNum ... Please include the missing sections and then attempt again.",code:422);
            }
            //is there any duplicate sections?     
            if(count(array_unique($req_sections_ids))
            !=count($req_sections_ids)){
                DB::rollBack();
                res::error("Duplicate section mark..fix it then try again.",code:422);
            }
            //does every mark matche the range of values for it's at_section?
            foreach($entry['sections_marks'] as $mark){
                $sec=AtSection::find($mark['at_section_id']);
                if($mark['mark']>$sec->max_mark){
                    DB::rollBack();
                    res::error("in entry number: $entryNum ... \"{$sec->name}\" mark should be "
                    ."less or equal to ( {$sec->max_mark} )"
                    ,code:422);
                }
            }
            $entry['student_type'] = $is_cand ? 
            CandidateStudent::class :
            Student::class;
            
            //is this student mark duplicated or already exists?
            $exist = AtMark::where('student_id',$student->id)->where('date',$entry['date']);
            $exist = $is_cand ?
                $exist->where('student_type',CandidateStudent::class)->first() :
                $exist->where('student_type',Student::class)->first();
            if(isset($exist)){
                DB::rollBack();
                res::error("in entry number: $entryNum ... this student mark already exists or is duplicated in this request.");
            }
        //EVERTHING IS GOOD!!.Now create the data
            try{
                $result=[];
                $at_mark=AtMark::create(
                    array_diff_key($entry,['sections_marks'=>'', 'is_candidate' => ''])
                );
                foreach($entry['sections_marks'] as $mark){
                    $mark['at_mark_id']=$at_mark->id;
                    $result[]=AtMarkSection::create($mark);
                }
            }catch(QueryException $e){
                res::queryError($e,rollback:true);
            }
            $finalResult[]=[
                'student_id' => $student->id,
                'student_name' => $student->first_name.' '.$student->last_name,
                'at_mark'=>$at_mark,
                'at_mark_sections'=>$result
            ];
            $count++;
        }
        DB::commit();   
        res::success(data:$finalResult);
    }
}
