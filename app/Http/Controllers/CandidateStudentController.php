<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Helpers\AtPerformance;
use Illuminate\Validation\Rule;
use App\Models\CandidateStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as res;

class CandidateStudentController extends Controller
{
    public function all(Grade $grade){
        $data= request()->validate([
            'min_percentage'=>['required','numeric','between:1,100']
        ]);
        $min_percentage=$data['min_percentage'];
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
            AtPerformance::assignStudentAcceptanceRate($candidate,$min_percentage);
        }
        $candidates->makeHidden([
            'atMarks'
        ]);
        AtPerformance::sortByAcceptanceRate($candidates);
        res::success(data:$candidates);

    }
    public function candidatesToOfficials(Grade $grade){
        $exists=Rule::exists('candidate_students','id')
        ->where('grade_id',$grade->id);
        $data=request()->validate([
            'ids'=>['required','array','min:1'],
            'ids.*'=>[$exists,'distinct']
        ],[
            'ids.*.exists'=>"The provided ID :input does not ".
            "belong to any candidate student in grade $grade->name .",
            'ids.*.distinct'=>"The provided student with ID :input is ".
            "duplicated in the input."
        ]);
        $cands=CandidateStudent::whereIn('id',$data['ids'])->get();
        foreach($cands as $cand){
            if(!$cand->canBecomeOfficial()){
                res::error($cand->conversionDuplicateErrorMsg(),422);
            }
        }
        DB::beginTransaction();
        $ctr=0;
        $official_students=[];
        try{
            
            foreach($cands as $cand){
                $official_students[]=$cand->makeHimOfficial();
                $ctr++;
            }
        }catch(QueryException $e){
            $cand=$cands[$ctr];
            res::queryError($e,duplicate:$cands->conversionDuplicateErrorMsg(),rollback:true);
        }
        DB::commit();
        res::success("Success! $ctr students became official!",[
            'official_students'=>$official_students
        ]);
        
    }
    
}
