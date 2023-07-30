<?php

namespace App\Http\Controllers;

use App\Models\AbilityTest;
use App\Models\Grade;
use Illuminate\Http\Request;

class CandidateStudentController extends Controller
{
    public function all(Grade $grade){
        $grade->load(['candidates:id,first_name,last_name,grade_id']);
        $candidates=$grade->candidates;
        $candidates->load([
            'atMarks'=>fn($query)=>
            $query->where('is_entry_mark',true),
            'atMarks.sections',
            'atMarks.sections.atSection:id,max_mark,min_mark'
        ]);
        
        foreach($candidates as $candidate){
            foreach($candidate->atMarks as $mark){
                foreach($mark->sections as $section){
                    //TODO: CONTINUE WORKING ON THIS
                }
            }
        }
        return $candidates;
    }
}
