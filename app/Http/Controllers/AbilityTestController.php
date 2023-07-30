<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res;
use App\Models\AbilityTest;
use App\Models\AtSection;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PHPUnit\TextUI\Help;

class AbilityTestController extends Controller
{
    public function add(Subject $subject) {
     //Validation..
        $unique=Rule::unique('ability_tests','title')
        ->where('subject_id',$subject->id);
        $data=request()->validate([
            'title'=>['required','string','min:2',$unique],
            'is_entry_test'=>['required','boolean'],
            'sections'=>['array','required','min:1'],
            'sections.*.name'=>['required','between:2,45','string'],
            'sections.*.min_mark'=>['required','numeric'],
            'sections.*.max_mark'=>['required','numeric','gt:sections.*.min_mark'],
        ],[
            'title.unique'=>'This subject already has an ability test called ":input"'
        ]);
     //Check that it wont make duplicate entryTests 
        // $count=$subject->ability_tests()
        // ->where('is_entry_test',true)
        // ->count();
        // if($data['is_entry_test']&&$count>0)
        // res::error("This subject already has an entry test form");
        
     //try inserting the data
        DB::beginTransaction();
        $ctr=0;
        try {
            $data['subject_id']=$subject->id;
            $at=AbilityTest::create($data);
            $sections=[];
            foreach ($data['sections'] as $section) {
                $section['ability_test_id']=$at->id;
                $sections[]=AtSection::create($section);
                $ctr++;
            }
        } catch (QueryException $e) {
            $dupMsg="Duplicate error.. \"{$data['sections'][$ctr]['name']}\" is duplicated in input";
            res::queryError($e,$dupMsg,rollback:true);
        }
        DB::commit();
        res::success(data:[
            'ability_test'=>$at,
            'sections'=>$sections
        ]);
    }
    public function viewSubjectsAbilityTests(Subject $subject){
        return $subject->ability_tests;
    }
    
}
