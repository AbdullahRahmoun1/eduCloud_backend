<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\AbilityTest;
use App\Models\Grade;
use Illuminate\Http\Request;

class CandidateStudentController extends Controller
{
    public function all(Grade $grade){
        $min_percentage= request()->validate([
            'min_percentage'=>['required','numeric','between:0.01,1']
        ]);
        $min_percentage=$min_percentage['min_percentage'];
        return $min_percentage;
        $grade->load(['candidates:id,first_name,last_name,grade_id']);
        $candidates=$grade->candidates;
        $candidates->load([
            'atMarks'=>fn($query)=>
            $query->where('is_entry_mark',true),
            'atMarks.sections',
            'atMarks.sections.atSection:id,max_mark,min_mark',
            'atMarks.abilityTest','atMarks.abilityTest.subject:id,name'
        ]);
        
        foreach($candidates as $candidate){
            $max=0;
            $got=0;
            $succeeded_subjects=[];
            $failed_subjects=[];
            $error_in=[];
            foreach($candidate->atMarks as $mark){
                $mark_max=0;
                $mark_got=0;
                foreach($mark->sections as $section){
                    $mark_max+=$section->atSection->max_mark;
                    $mark_got+=$section->mark;
                }
                $sub=$mark->abilityTest->subject->name;
                if($mark_max>0){
                    if($mark_got/($mark_max*0.1)>=$mark->abilityTest->minimum_success_percentage){
                        $succeeded_subjects[]=$sub;
                    }else{
                        $failed_subjects[]=$sub;
                    }
                }else{
                    $error_in[]=$sub;
                }
                $max+=$mark_max;
                $got+=$mark_got;
            }
            sort($succeeded_subjects);
            sort($failed_subjects);
            $candidate->full_name=$candidate->full_name();  
            $have_one_test=$max>0;
            if($have_one_test){
                $f=$got/($max*1.0)*100;
                $candidate->final_result=number_format($f, 2);
            }else {
                $candidate->final_result="N/A";
            }
            $candidate->succeeded=$have_one_test?
                $candidate->final_result>=$min_percentage:"N/A";
            $candidate->succeeded_in=$succeeded_subjects;
            $candidate->failed_in=$failed_subjects;
            $candidate->error_in=$error_in;
        }
        $candidates->makeHidden([
            'first_name','last_name','atMarks'
        ]);
        $good=$candidates->filter(fn($cand)=>$cand->final_result!="N/A");
        $good = $good->sortByDesc(function ($item) {
            $successes=count($item['succeeded_in']);
            $failes=count($item['failed_in']);
            return [
                $item['final_result'],
                $successes / $successes + $failes,
                $successes
            ];
        });
        $bad=$candidates->filter(fn($cand)=>$cand->final_result=="N/A");
        $candidates=$good->merge($bad);
        res::success(data:$candidates);

    }
    public function a(){
            
    }
    
}
